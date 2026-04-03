<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class LogoutController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
    ) {}

    /**
     * Logout and revoke the current access token
     *
     * Revokes only the token used for the current request.
     */
    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $this->service->logout($user);

        return response()->noContent();
    }
}
