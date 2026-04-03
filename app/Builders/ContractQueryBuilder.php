<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder;

final class ContractQueryBuilder
{
    /**
     * @param  Builder<Contract>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(Contract::query());
    }

    /**
     * @return Builder<Contract>
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

    public function withStatus(ContractStatus $status): self
    {
        $this->query->where('status', $status->value);

        return $this;
    }

    public function withRelease(): self
    {
        $this->query->with('release');

        return $this;
    }

    public function withUser(): self
    {
        $this->query->with('user');

        return $this;
    }

    public function latest(): self
    {
        $this->query->latest();

        return $this;
    }
}
