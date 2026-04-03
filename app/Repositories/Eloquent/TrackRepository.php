<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\TrackQueryBuilder;
use App\Models\Track;
use App\Repositories\Contracts\TrackRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class TrackRepository implements TrackRepositoryInterface
{
    public function findByKey(string $key): Track
    {
        $model = TrackQueryBuilder::make()
            ->byKey($key)
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("Track not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @return Collection<int, Track>
     */
    public function findByReleaseId(int $releaseId): Collection
    {
        return TrackQueryBuilder::make()
            ->byReleaseId($releaseId)
            ->orderedByTrackNumber()
            ->withRelease()
            ->getQuery()
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Track
    {
        return Track::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Track $track, array $data): Track
    {
        $track->fill($data);
        $track->save();

        return $track->refresh();
    }

    public function delete(Track $track): void
    {
        $track->delete();
    }

    public function countByReleaseId(int $releaseId): int
    {
        return TrackQueryBuilder::make()
            ->byReleaseId($releaseId)
            ->getQuery()
            ->count();
    }
}
