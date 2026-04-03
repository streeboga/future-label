<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentProviderInterface;
use App\DataTransferObjects\Payment\CreatePaymentData;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReleaseStatus;
use App\Models\Payment;
use App\Models\Release;
use App\Models\User;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ReleaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private ReleaseRepositoryInterface $releaseRepository,
        private PaymentProviderInterface $paymentProvider,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Payment>
     */
    public function listForUser(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->paymentRepository->paginateForUser($user->id, $filters, $perPage);
    }

    public function findByKey(string $key): Payment
    {
        return $this->paymentRepository->findByKey($key);
    }

    /**
     * Initiate a payment for a release.
     */
    public function initiatePayment(User $user, Release $release, CreatePaymentData $data): Payment
    {
        if ($release->status !== ReleaseStatus::AwaitingPayment) {
            throw ValidationException::withMessages([
                'release' => ['Release must be in awaiting_payment status to initiate payment.'],
            ]);
        }

        // Calculate amount from release services
        $amount = $this->calculateReleaseAmount($release);

        if ($data->method === PaymentMethod::Online) {
            return $this->initiateOnlinePayment($user, $release, $amount, $data->return_url);
        }

        return $this->initiateManualPayment($user, $release, $amount);
    }

    /**
     * Process webhook from payment provider.
     *
     * @param  array<string, mixed>  $webhookData
     */
    public function processWebhook(array $webhookData): Payment
    {
        $providerPaymentId = $webhookData['payment_id'] ?? null;

        if (! is_string($providerPaymentId) || $providerPaymentId === '') {
            throw ValidationException::withMessages([
                'payment_id' => ['Payment ID is required in webhook data.'],
            ]);
        }

        $payment = $this->paymentRepository->findByProviderPaymentId($providerPaymentId);

        if ($payment === null) {
            throw ValidationException::withMessages([
                'payment_id' => ["Payment not found for provider payment ID [{$providerPaymentId}]."],
            ]);
        }

        $status = $webhookData['status'] ?? null;
        $newStatus = is_string($status) ? PaymentStatus::tryFrom($status) : null;

        if ($newStatus === null) {
            throw ValidationException::withMessages([
                'status' => ['Invalid payment status in webhook data.'],
            ]);
        }

        if (! $payment->status->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot transition payment from {$payment->status->value} to {$newStatus->value}."],
            ]);
        }

        /** @var Payment */
        return DB::transaction(function () use ($payment, $newStatus, $webhookData): Payment {
            $updateData = [
                'status' => $newStatus,
                'provider_data' => $webhookData,
            ];

            if (isset($webhookData['receipt_url']) && is_string($webhookData['receipt_url'])) {
                $updateData['receipt_url'] = $webhookData['receipt_url'];
            }

            $payment = $this->paymentRepository->update($payment, $updateData);

            // Transition release status when payment is successful
            if ($newStatus === PaymentStatus::Paid && $payment->release_id !== null) {
                $this->transitionReleaseAfterPayment($payment);
            }

            return $payment;
        });
    }

    /**
     * Admin/Manager confirms a manual payment.
     */
    public function confirmPayment(Payment $payment, User $confirmedBy): Payment
    {
        if ($payment->method !== PaymentMethod::Manual) {
            throw ValidationException::withMessages([
                'method' => ['Only manual payments can be confirmed by a manager.'],
            ]);
        }

        if (! $payment->status->canTransitionTo(PaymentStatus::Confirmed)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot confirm payment in {$payment->status->value} status."],
            ]);
        }

        /** @var Payment */
        return DB::transaction(function () use ($payment, $confirmedBy): Payment {
            $payment = $this->paymentRepository->update($payment, [
                'status' => PaymentStatus::Confirmed,
                'confirmed_by' => $confirmedBy->id,
                'confirmed_at' => now(),
            ]);

            // Transition release status when payment is confirmed
            if ($payment->release_id !== null) {
                $this->transitionReleaseAfterPayment($payment);
            }

            return $payment;
        });
    }

    /**
     * Admin/Manager rejects a payment.
     */
    public function rejectPayment(Payment $payment): Payment
    {
        if (! $payment->status->canTransitionTo(PaymentStatus::Failed)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot reject payment in {$payment->status->value} status."],
            ]);
        }

        /** @var Payment */
        return DB::transaction(fn (): Payment => $this->paymentRepository->update($payment, [
            'status' => PaymentStatus::Failed,
        ]));
    }

    private function initiateOnlinePayment(User $user, Release $release, string $amount, ?string $returnUrl): Payment
    {
        $description = "Payment for release: {$release->title}";
        $providerResult = $this->paymentProvider->createPayment(
            $amount,
            'RUB',
            $description,
            $returnUrl ?? config('app.url', 'http://localhost'),
        );

        if (! $providerResult->success) {
            throw ValidationException::withMessages([
                'payment' => [$providerResult->errorMessage ?? 'Payment provider error.'],
            ]);
        }

        /** @var Payment */
        return DB::transaction(fn (): Payment => $this->paymentRepository->create([
            'user_id' => $user->id,
            'release_id' => $release->id,
            'amount' => $amount,
            'currency' => 'RUB',
            'method' => PaymentMethod::Online,
            'status' => PaymentStatus::Processing,
            'provider' => 'stub',
            'provider_payment_id' => $providerResult->paymentId,
            'receipt_url' => $providerResult->receiptUrl,
            'provider_data' => $providerResult->providerData,
        ]));
    }

    private function initiateManualPayment(User $user, Release $release, string $amount): Payment
    {
        /** @var Payment */
        return DB::transaction(fn (): Payment => $this->paymentRepository->create([
            'user_id' => $user->id,
            'release_id' => $release->id,
            'amount' => $amount,
            'currency' => 'RUB',
            'method' => PaymentMethod::Manual,
            'status' => PaymentStatus::Pending,
            'provider' => null,
            'provider_payment_id' => null,
        ]));
    }

    private function calculateReleaseAmount(Release $release): string
    {
        // Load release services with prices for calculation
        $release->loadMissing('services');

        $total = $release->services->sum('price');

        // Minimum amount if no services configured
        if ($total <= 0) {
            $total = 1000.00;
        }

        return number_format((float) $total, 2, '.', '');
    }

    private function transitionReleaseAfterPayment(Payment $payment): void
    {
        $release = $payment->release;

        if ($release === null) {
            return;
        }

        $release = $release->refresh();

        if ($release->status === ReleaseStatus::AwaitingPayment) {
            $targetStatus = ReleaseStatus::InReview;

            if ($release->status->canTransitionTo($targetStatus)) {
                $this->releaseRepository->updateStatus($release, $targetStatus);
            }
        }
    }
}
