<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AdminUserController extends Controller
{
    public function __construct(
        private readonly UserService $service,
    ) {}

    /**
     * List all users (admin)
     *
     * Returns paginated list of all users with optional search.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [];

        $search = $request->query('search');
        if (is_string($search) && $search !== '') {
            $filters['search'] = $search;
        }

        $perPage = min((int) ($request->query('per_page', '15')), 100);

        $users = $this->service->listAll($filters, $perPage);

        return UserResource::collection($users);
    }

    /**
     * Show user detail (admin)
     *
     * Returns a single user with their releases.
     */
    public function show(User $user): UserResource
    {
        $user->load('releases');

        return UserResource::make($user);
    }
}
