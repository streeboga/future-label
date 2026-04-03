<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\User\CreateUserData;
use App\DataTransferObjects\User\UpdateProfileData;
use App\Enums\UserRole;
use App\Events\UserRegistered;
use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;

final readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    /**
     * Register a new user with the artist role.
     *
     * @return array{user: User, token: string}
     */
    public function register(CreateUserData $data): array
    {
        /** @var array{user: User, token: string} $result */
        $result = DB::transaction(function () use ($data): array {
            $user = $this->repository->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
                'role' => UserRole::Artist,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });

        $result['user']->notify(new VerifyEmail);

        event(new UserRegistered($result['user']));

        return $result;
    }

    public function updateProfile(User $user, UpdateProfileData $data, ?string $ipAddress = null): User
    {
        $updateData = collect($data->toArray())
            ->reject(fn (mixed $value): bool => $value instanceof Optional)
            ->toArray();

        /** @var User $updatedUser */
        $updatedUser = DB::transaction(function () use ($user, $updateData, $ipAddress): User {
            $updatedUser = $this->repository->update($user, $updateData);

            $this->logActivity($user, 'profile.updated', $ipAddress);

            return $updatedUser;
        });

        return $updatedUser;
    }

    public function logProfileViewed(User $actor, User $subject, ?string $ipAddress = null): void
    {
        $this->logActivity($actor, 'profile.viewed', $ipAddress, $subject);
    }

    private function logActivity(User $actor, string $action, ?string $ipAddress = null, ?User $subject = null): void
    {
        ActivityLog::create([
            'user_id' => $actor->id,
            'subject_type' => User::class,
            'subject_id' => ($subject ?? $actor)->id,
            'action' => $action,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }
}
