<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\ReleaseQueryBuilder;
use App\Enums\ReleaseStatus;
use App\Models\Release;
use App\Repositories\Contracts\ReleaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ReleaseRepository implements ReleaseRepositoryInterface
{
    public function findByKey(string $key): Release
    {
        $model = ReleaseQueryBuilder::make()
            ->byKey($key)
            ->withTracks()
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("Release not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Release>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $builder = ReleaseQueryBuilder::make()->latest();

        $this->applyFilters($builder, $filters);

        return $builder->getQuery()->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Release>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $builder = ReleaseQueryBuilder::make()
            ->byUserId($userId)
            ->latest();

        $this->applyFilters($builder, $filters);

        return $builder->getQuery()->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Release
    {
        return Release::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Release $release, array $data): Release
    {
        $release->fill($data);
        $release->save();

        return $release->refresh();
    }

    public function delete(Release $release): void
    {
        $release->delete();
    }

    public function updateStatus(Release $release, ReleaseStatus $status): Release
    {
        $release->status = $status;
        $release->save();

        return $release->refresh();
    }

    public function countTracksForRelease(int $releaseId): int
    {
        $release = Release::where('id', $releaseId)->withCount('tracks')->first();

        if ($release === null) {
            return 0;
        }

        /** @var int */
        return $release->tracks_count;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(ReleaseQueryBuilder $builder, array $filters): void
    {
        if (isset($filters['status']) && $filters['status'] instanceof ReleaseStatus) {
            $builder->withStatus($filters['status']);
        }

        if (isset($filters['search']) && is_string($filters['search'])) {
            $builder->search($filters['search']);
        }
    }
}
