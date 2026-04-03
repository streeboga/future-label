<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;

final class PaymentQueryBuilder
{
    /**
     * @param  Builder<Payment>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(Payment::query());
    }

    /**
     * @return Builder<Payment>
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function byKey(string $key): self
    {
        $this->query->where('key', $key);

        return $this;
    }

    public function byUserId(int $userId): self
    {
        $this->query->where('user_id', $userId);

        return $this;
    }

    public function byReleaseId(int $releaseId): self
    {
        $this->query->where('release_id', $releaseId);

        return $this;
    }

    public function withStatus(PaymentStatus $status): self
    {
        $this->query->where('status', $status->value);

        return $this;
    }

    public function withMethod(PaymentMethod $method): self
    {
        $this->query->where('method', $method->value);

        return $this;
    }

    public function byProviderPaymentId(string $providerPaymentId): self
    {
        $this->query->where('provider_payment_id', $providerPaymentId);

        return $this;
    }

    public function withUser(): self
    {
        $this->query->with('user');

        return $this;
    }

    public function withRelease(): self
    {
        $this->query->with('release');

        return $this;
    }

    public function latest(): self
    {
        $this->query->latest();

        return $this;
    }
}
