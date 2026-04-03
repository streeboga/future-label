<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\PaymentQueryBuilder;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class PaymentRepository implements PaymentRepositoryInterface
{
    private function query(): PaymentQueryBuilder
    {
        return PaymentQueryBuilder::make();
    }

    public function findByKey(string $key): Payment
    {
        $model = $this->query()
            ->byKey($key)
            ->withRelease()
            ->withUser()
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("Payment not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Payment>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $builder = $this->query()
            ->byUserId($userId)
            ->withRelease()
            ->latest();

        $this->applyFilters($builder, $filters);

        return $builder->getQuery()->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Payment $payment, array $data): Payment
    {
        $payment->fill($data);
        $payment->save();

        return $payment->refresh();
    }

    public function updateStatus(Payment $payment, PaymentStatus $status): Payment
    {
        $payment->status = $status;
        $payment->save();

        return $payment->refresh();
    }

    public function findByProviderPaymentId(string $providerPaymentId): ?Payment
    {
        return $this->query()
            ->byProviderPaymentId($providerPaymentId)
            ->getQuery()
            ->first();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(PaymentQueryBuilder $builder, array $filters): void
    {
        if (isset($filters['status']) && $filters['status'] instanceof PaymentStatus) {
            $builder->withStatus($filters['status']);
        }
    }
}
