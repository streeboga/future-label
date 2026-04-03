<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function findByKey(string $key): Payment;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Payment>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Payment;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Payment $payment, array $data): Payment;

    public function updateStatus(Payment $payment, PaymentStatus $status): Payment;

    public function findByProviderPaymentId(string $providerPaymentId): ?Payment;

    public function sumConfirmedAmount(): string;
}
