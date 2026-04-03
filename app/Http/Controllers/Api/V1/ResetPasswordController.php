<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
    ) {}

    /**
     * Reset user password
     *
     * Resets the user's password using the provided token from the reset email.
     */
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $this->service->resetPassword(
            $request->validated('email'),
            $request->validated('token'),
            $request->validated('password'),
        );

        return response()->json([
            'meta' => [
                'message' => 'Password has been reset successfully.',
            ],
        ]);
    }
}
