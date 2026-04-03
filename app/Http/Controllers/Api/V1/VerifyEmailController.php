<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
    ) {}

    /**
     * Verify user email address
     *
     * Accepts a signed URL with user id and email hash.
     * Marks the user's email as verified.
     */
    public function __invoke(Request $request, int $id, string $hash): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            abort(404, 'User not found.');
        }

        if (! hash_equals(sha1($user->email), $hash)) {
            abort(403, 'Invalid verification link.');
        }

        $this->service->verifyEmail($user);

        return response()->json([
            'data' => [
                'message' => 'Email verified successfully.',
            ],
        ]);
    }
}
