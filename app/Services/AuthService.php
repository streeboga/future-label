<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

final readonly class AuthService
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    /**
     * Authenticate user and create a new Sanctum token.
     *
     * @return array{user: User, token: string}
     *
     * @throws AuthenticationException
     */
    public function login(string $email, string $password): array
    {
        $user = $this->repository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('The provided credentials are incorrect.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Revoke the current access token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Verify user's email address.
     */
    public function verifyEmail(User $user): void
    {
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    }
}
