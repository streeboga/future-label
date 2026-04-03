<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\ContractQueryBuilder;
use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ContractRepository implements ContractRepositoryInterface
{
    public function findByKey(string $key): Contract
    {
        $model = ContractQueryBuilder::make()
            ->byKey($key)
            ->withRelease()
            ->withUser()
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("Contract not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Contract>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $builder = ContractQueryBuilder::make()
            ->byUserId($userId)
            ->withRelease()
            ->latest();

        $this->applyFilters($builder, $filters);

        return $builder->getQuery()->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Contract>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $builder = ContractQueryBuilder::make()
            ->withRelease()
            ->withUser()
            ->latest();

        $this->applyFilters($builder, $filters);

        return $builder->getQuery()->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Contract
    {
        return Contract::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Contract $contract, array $data): Contract
    {
        $contract->fill($data);
        $contract->save();

        return $contract->refresh();
    }

    public function updateStatus(Contract $contract, ContractStatus $status): Contract
    {
        $contract->status = $status;
        $contract->save();

        return $contract->refresh();
    }

    public function findAcceptedForRelease(int $releaseId): ?Contract
    {
        return ContractQueryBuilder::make()
            ->byReleaseId($releaseId)
            ->withStatus(ContractStatus::Accepted)
            ->getQuery()
            ->first();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(ContractQueryBuilder $builder, array $filters): void
    {
        if (isset($filters['status']) && $filters['status'] instanceof ContractStatus) {
            $builder->withStatus($filters['status']);
        }

        if (isset($filters['release_id']) && is_int($filters['release_id'])) {
            $builder->byReleaseId($filters['release_id']);
        }
    }
}
