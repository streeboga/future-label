<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReleaseStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Release\StoreReleaseRequest;
use App\Http\Requests\Release\UpdateReleaseRequest;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use App\Models\User;
use App\Services\ReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

final class ReleaseController extends Controller
{
    public function __construct(
        private readonly ReleaseService $service,
    ) {}

    /**
     * List releases
     *
     * Returns paginated list of releases. Artists see only their own releases.
     * Admins and managers see all releases.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        Gate::authorize('viewAny', Release::class);

        $filters = [];

        $statusFilter = $request->query('filter');
        if (is_array($statusFilter) && isset($statusFilter['status']) && is_string($statusFilter['status'])) {
            $status = ReleaseStatus::tryFrom($statusFilter['status']);
            if ($status !== null) {
                $filters['status'] = $status;
            }
        }

        $perPage = min((int) ($request->query('per_page', '15')), 100);

        if ($user->role === UserRole::Admin || $user->role === UserRole::Manager) {
            $releases = $this->service->listAll($filters, $perPage);
        } else {
            $releases = $this->service->listForUser($user, $filters, $perPage);
        }

        return ReleaseResource::collection($releases);
    }

    /**
     * Show a release
     *
     * Returns a single release with its tracks.
     */
    public function show(Release $release): ReleaseResource
    {
        Gate::authorize('view', $release);

        $release->load('tracks');

        return ReleaseResource::make($release);
    }

    /**
     * Create a release
     *
     * Creates a new release in draft status.
     */
    public function store(StoreReleaseRequest $request): JsonResponse
    {
        Gate::authorize('create', Release::class);

        /** @var User $user */
        $user = $request->user();

        $release = $this->service->create($user, $request->toDto());

        return ReleaseResource::make($release)
            ->response()
            ->setStatusCode(201)
            ->header('Location', "/api/v1/releases/{$release->key}");
    }

    /**
     * Update a release
     *
     * Updates release fields. Only allowed in draft or rejected status.
     */
    public function update(UpdateReleaseRequest $request, Release $release): ReleaseResource
    {
        Gate::authorize('update', $release);

        $updatedRelease = $this->service->update($release, $request->toDto());

        return ReleaseResource::make($updatedRelease);
    }

    /**
     * Delete a release
     *
     * Deletes a release. Only allowed for drafts.
     */
    public function destroy(Release $release): JsonResponse
    {
        Gate::authorize('delete', $release);

        $this->service->delete($release);

        return response()->json(null, 204);
    }

    /**
     * Submit a release for review
     *
     * Transitions the release through the status machine.
     */
    public function submit(Release $release): ReleaseResource
    {
        Gate::authorize('submit', $release);

        $updatedRelease = $this->service->submit($release);

        return ReleaseResource::make($updatedRelease);
    }
}
