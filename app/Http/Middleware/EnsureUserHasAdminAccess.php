<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserHasAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * Allows access only for users with admin or manager role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user instanceof User) {
            return new JsonResponse([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (! in_array($user->role, [UserRole::Admin, UserRole::Manager], true)) {
            return new JsonResponse([
                'message' => 'Access denied. Admin or manager role required.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
