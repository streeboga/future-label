<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ReleaseStatus;
use App\Models\Release;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReleaseRepositoryInterface
{
    public function findByKey(string $key): Release;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Release>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Release>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Release;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Release $release, array $data): Release;

    public function delete(Release $release): void;

    public function updateStatus(Release $release, ReleaseStatus $status): Release;

    public function countTracksForRelease(int $releaseId): int;

    public function countByStatus(ReleaseStatus $status): int;

    public function countCreatedThisMonth(): int;
}
