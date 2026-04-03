<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class ForgotPasswordController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
    ) {}

    /**
     * Send a password reset link
     *
     * Sends a password reset email to the given address if it exists.
     * Always returns 200 to prevent email enumeration.
     */
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $this->service->sendResetLink($request->validated('email'));

        return response()->json([
            'meta' => [
                'message' => 'If the email exists, a reset link has been sent.',
            ],
        ]);
    }
}
