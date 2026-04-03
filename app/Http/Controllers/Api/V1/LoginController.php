<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

final class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
    ) {}

    /**
     * Authenticate user and return a bearer token
     *
     * Validates credentials and issues a Sanctum personal access token.
     *
     * @throws AuthenticationException
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return UserResource::make($result['user'])
            ->additional(['meta' => ['token' => $result['token']]])
            ->response();
    }
}
