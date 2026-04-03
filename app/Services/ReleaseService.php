<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Release\CreateReleaseData;
use App\DataTransferObjects\Release\UpdateReleaseData;
use App\Enums\ReleaseStatus;
use App\Events\ReleaseStatusChanged;
use App\Events\ReleaseSubmittedForReview;
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

    public function approve(Release $release): Release
    {
        if ($release->status !== ReleaseStatus::InReview) {
            throw ValidationException::withMessages([
                'status' => ['Release can only be approved from in_review status.'],
            ]);
        }

        $oldStatus = $release->status;

        /** @var Release */
        $release = DB::transaction(fn (): Release => $this->repository->updateStatus($release, ReleaseStatus::Approved));

        ReleaseStatusChanged::dispatch($release, $oldStatus, ReleaseStatus::Approved);

        return $release;
    }

    public function reject(Release $release, string $reason): Release
    {
        if ($release->status !== ReleaseStatus::InReview) {
            throw ValidationException::withMessages([
                'status' => ['Release can only be rejected from in_review status.'],
            ]);
        }

        $oldStatus = $release->status;

        /** @var Release */
        $release = DB::transaction(function () use ($release, $reason): Release {
            $release->reject_reason = $reason;
            $release->save();

            return $this->repository->updateStatus($release, ReleaseStatus::Rejected);
        });

        ReleaseStatusChanged::dispatch($release, $oldStatus, ReleaseStatus::Rejected);

        return $release;
    }

    public function publish(Release $release): Release
    {
        if ($release->status !== ReleaseStatus::Approved) {
            throw ValidationException::withMessages([
                'status' => ['Release can only be published from approved status.'],
            ]);
        }

        $oldStatus = $release->status;

        /** @var Release */
        $release = DB::transaction(function () use ($release): Release {
            $release->published_at = now();
            $release->save();

            return $this->repository->updateStatus($release, ReleaseStatus::Published);
        });

        ReleaseStatusChanged::dispatch($release, $oldStatus, ReleaseStatus::Published);

        return $release;
    }

    /**
     * Submit release — transitions Draft → InReview.
     * Payment and contracts are handled separately (invoiced manually for now).
     */
    public function submit(Release $release): Release
    {
        if ($release->status !== ReleaseStatus::Draft && $release->status !== ReleaseStatus::Rejected) {
            throw ValidationException::withMessages([
                'status' => ['Release can only be submitted from draft or rejected status.'],
            ]);
        }

        $oldStatus = $release->status;

        // If rejected, first transition back to draft
        if ($release->status === ReleaseStatus::Rejected) {
            $release = $this->repository->updateStatus($release, ReleaseStatus::Draft);
        }

        $targetStatus = ReleaseStatus::InReview;

        if (! $release->status->canTransitionTo($targetStatus)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot transition from {$release->status->value} to {$targetStatus->value}."],
            ]);
        }

        /** @var Release */
        $release = DB::transaction(function () use ($release, $targetStatus): Release {
            $release->submitted_at = now();
            $release->reject_reason = null;
            $release->save();

            return $this->repository->updateStatus($release, $targetStatus);
        });

        ReleaseSubmittedForReview::dispatch($release);
        ReleaseStatusChanged::dispatch($release, $oldStatus, $targetStatus);

        return $release;
    }

    /**
     * Transition release after payment is confirmed.
     * AwaitingPayment → AwaitingContract.
     */
    public function transitionAfterPayment(Release $release): Release
    {
        if ($release->status !== ReleaseStatus::AwaitingPayment) {
            return $release;
        }

        $oldStatus = $release->status;
        $targetStatus = ReleaseStatus::AwaitingContract;

        /** @var Release */
        $release = DB::transaction(fn (): Release => $this->repository->updateStatus($release, $targetStatus));

        ReleaseStatusChanged::dispatch($release, $oldStatus, $targetStatus);

        return $release;
    }

    /**
     * Transition release after contract is accepted.
     * AwaitingContract → InReview.
     */
    public function transitionAfterContract(Release $release): Release
    {
        if ($release->status !== ReleaseStatus::AwaitingContract) {
            return $release;
        }

        $oldStatus = $release->status;
        $targetStatus = ReleaseStatus::InReview;

        /** @var Release */
        $release = DB::transaction(fn (): Release => $this->repository->updateStatus($release, $targetStatus));

        ReleaseStatusChanged::dispatch($release, $oldStatus, $targetStatus);

        return $release;
    }

    /**
     * Attach services to a release.
     *
     * @param  array<int, int>  $serviceIds
     */
    public function syncServices(Release $release, array $serviceIds): Release
    {
        if ($release->status !== ReleaseStatus::Draft && $release->status !== ReleaseStatus::Rejected) {
            throw ValidationException::withMessages([
                'status' => ['Services can only be modified on draft or rejected releases.'],
            ]);
        }

        DB::transaction(fn () => $release->services()->sync($serviceIds));

        return $release->load('services');
    }
}
