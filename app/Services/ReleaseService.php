<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Release\CreateReleaseData;
use App\DataTransferObjects\Release\UpdateReleaseData;
use App\Enums\ReleaseStatus;
use App\Models\Release;
use App\Models\User;
use App\Repositories\Contracts\ReleaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

final readonly class ReleaseService
{
    public function __construct(
        private ReleaseRepositoryInterface $repository,
    ) {}

    public function create(User $user, CreateReleaseData $data): Release
    {
        /** @var Release */
        return DB::transaction(fn (): Release => $this->repository->create([
            'user_id' => $user->id,
            'title' => $data->title,
            'type' => $data->type,
            'artist_name' => $data->artist_name,
            'genre' => $data->genre,
            'language' => $data->language,
            'description' => $data->description,
            'release_date' => $data->release_date,
            'cover_url' => $data->cover_url,
            'metadata' => $data->metadata,
            'status' => ReleaseStatus::Draft,
        ]));
    }

    public function update(Release $release, UpdateReleaseData $data): Release
    {
        if ($release->status !== ReleaseStatus::Draft && $release->status !== ReleaseStatus::Rejected) {
            throw ValidationException::withMessages([
                'status' => ['Release can only be edited in draft or rejected status.'],
            ]);
        }

        $updateData = collect($data->toArray())
            ->reject(fn (mixed $value): bool => $value instanceof Optional)
            ->toArray();

        /** @var Release */
        return DB::transaction(fn (): Release => $this->repository->update($release, $updateData));
    }

    public function delete(Release $release): void
    {
        if ($release->status !== ReleaseStatus::Draft) {
            throw ValidationException::withMessages([
                'status' => ['Only draft releases can be deleted.'],
            ]);
        }

        DB::transaction(fn () => $this->repository->delete($release));
    }

    public function findByKey(string $key): Release
    {
        return $this->repository->findByKey($key);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Release>
     */
    public function listForUser(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateForUser($user->id, $filters, $perPage);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Release>
     */
    public function listAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function submit(Release $release): Release
    {
        if ($release->status !== ReleaseStatus::Draft && $release->status !== ReleaseStatus::Rejected) {
            throw ValidationException::withMessages([
                'status' => ['Release can only be submitted from draft or rejected status.'],
            ]);
        }

        // If rejected, first transition back to draft
        if ($release->status === ReleaseStatus::Rejected) {
            $release = $this->repository->updateStatus($release, ReleaseStatus::Draft);
        }

        // Determine transition target based on conditions
        // For simplicity: go directly to InReview (real logic would check payment/contract)
        $targetStatus = ReleaseStatus::InReview;

        if (! $release->status->canTransitionTo($targetStatus)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot transition from {$release->status->value} to {$targetStatus->value}."],
            ]);
        }

        /** @var Release */
        return DB::transaction(function () use ($release, $targetStatus): Release {
            $release->submitted_at = now();
            $release->reject_reason = null;
            $release->save();

            return $this->repository->updateStatus($release, $targetStatus);
        });
    }
}
