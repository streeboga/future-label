<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly UserService $service,
    ) {}

    /**
     * Register a new artist
     *
     * Create a new user account with the artist role.
     * Returns the user resource and a bearer token for authentication.
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $result = $this->service->register($request->toDto());

        return UserResource::make($result['user'])
            ->additional(['meta' => ['token' => $result['token']]])
            ->response()
            ->setStatusCode(201)
            ->header('Location', "/api/v1/users/{$result['user']->key}");
    }
}
