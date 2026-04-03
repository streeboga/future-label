<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;

interface TrackRepositoryInterface
{
    public function findByKey(string $key): Track;

    /**
     * @return Collection<int, Track>
     */
    public function findByReleaseId(int $releaseId): Collection;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Track;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Track $track, array $data): Track;

    public function delete(Track $track): void;

    public function countByReleaseId(int $releaseId): int;
}
