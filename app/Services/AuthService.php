<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    /**
     * Send a password reset link to the given email.
     *
     * @throws ValidationException
     */
    public function sendResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        // For any other status (including INVALID_USER), we silently succeed
        // to prevent email enumeration attacks.
    }

    /**
     * Reset the user's password using the given token.
     *
     * @throws ValidationException
     */
    public function resetPassword(string $email, string $token, string $password): void
    {
        $status = Password::reset(
            [
                'email' => $email,
                'token' => $token,
                'password' => $password,
                'password_confirmation' => $password,
            ],
            function (User $user, string $password): void {
                $this->repository->updatePassword($user, $password);

                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }
}
