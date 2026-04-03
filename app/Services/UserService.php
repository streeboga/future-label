<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\User\CreateUserData;
use App\Enums\UserRole;
use App\Events\UserRegistered;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($data): array {
            $user = $this->repository->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
                'role' => UserRole::Artist,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->notify(new VerifyEmail);

            event(new UserRegistered($user));

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}
