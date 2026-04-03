<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Track\CreateTrackData;
use App\DataTransferObjects\Track\UpdateTrackData;
use App\Enums\ReleaseStatus;
use App\Models\Release;
use App\Models\Track;
use App\Repositories\Contracts\TrackRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

final readonly class TrackService
{
    private const int MAX_TRACKS_PER_RELEASE = 30;

    public function __construct(
        private TrackRepositoryInterface $repository,
    ) {}

    public function create(Release $release, CreateTrackData $data): Track
    {
        if ($release->status !== ReleaseStatus::Draft && $release->status !== ReleaseStatus::Rejected) {
            throw ValidationException::withMessages([
                'status' => ['Tracks can only be added to releases in draft or rejected status.'],
            ]);
        }

        $currentCount = $this->repository->countByReleaseId($release->id);

        if ($currentCount >= self::MAX_TRACKS_PER_RELEASE) {
            throw ValidationException::withMessages([
                'tracks' => ['A release cannot have more than '.self::MAX_TRACKS_PER_RELEASE.' tracks.'],
            ]);
        }

        $trackNumber = $data->track_number ?? ($currentCount + 1);

        /** @var Track */
        return DB::transaction(fn (): Track => $this->repository->create([
            'release_id' => $release->id,
            'title' => $data->title,
            'format' => $data->format,
            'track_number' => $trackNumber,
            'duration_seconds' => $data->duration_seconds,
            'file_url' => $data->file_url,
            'file_size' => $data->file_size,
            'authors' => $data->authors,
            'composers' => $data->composers,
            'lyrics' => $data->lyrics,
            'isrc' => $data->isrc,
        ]));
    }

    public function update(Track $track, UpdateTrackData $data): Track
    {
        $updateData = collect($data->toArray())
            ->reject(fn (mixed $value): bool => $value instanceof Optional)
            ->toArray();

        /** @var Track */
        return DB::transaction(fn (): Track => $this->repository->update($track, $updateData));
    }

    public function delete(Release $release, Track $track): void
    {
        if ($release->status !== ReleaseStatus::Draft && $release->status !== ReleaseStatus::Rejected) {
            throw ValidationException::withMessages([
                'status' => ['Tracks can only be removed from releases in draft or rejected status.'],
            ]);
        }

        DB::transaction(fn () => $this->repository->delete($track));
    }

    /**
     * @return Collection<int, Track>
     */
    public function listForRelease(Release $release): Collection
    {
        return $this->repository->findByReleaseId($release->id);
    }

    public function findByKey(string $key): Track
    {
        return $this->repository->findByKey($key);
    }
}
