<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Current authenticated user
 */
final class MeController extends Controller
{
    /**
     * Get current user
     *
     * Returns the currently authenticated user with their role in JSON:API format.
     * Used by the frontend to determine interface routing after login.
     */
    public function __invoke(Request $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        return UserResource::make($user);
    }
}
