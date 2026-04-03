<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly UserService $service,
        private readonly UserRepositoryInterface $repository,
    ) {}

    /**
     * Show user profile
     *
     * Returns the authenticated user's profile, or a specific user's profile for admins.
     */
    public function show(Request $request, ?string $key = null): UserResource
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $user = $key !== null
            ? $this->repository->findByKey($key)
            : $authUser;

        Gate::authorize('view', $user);

        $this->service->logProfileViewed($authUser, $user, $request->ip());

        return UserResource::make($user);
    }

    /**
     * Update user profile
     *
     * Updates the authenticated user's profile, or a specific user's profile for admins.
     * Supports partial updates (PATCH). PII fields are encrypted at rest.
     */
    public function update(UpdateProfileRequest $request, ?string $key = null): UserResource
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $user = $key !== null
            ? $this->repository->findByKey($key)
            : $authUser;

        Gate::authorize('update', $user);

        $updatedUser = $this->service->updateProfile(
            $user,
            $request->toDto(),
            $request->ip(),
        );

        return UserResource::make($updatedUser);
    }
}
