<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContractRepositoryInterface
{
    public function findByKey(string $key): Contract;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Contract>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Contract>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Contract;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Contract $contract, array $data): Contract;

    public function updateStatus(Contract $contract, ContractStatus $status): Contract;

    public function findAcceptedForRelease(int $releaseId): ?Contract;
}
