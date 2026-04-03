<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class UserRepository implements UserRepositoryInterface
{
    public function findByKey(string $key): User
    {
        $model = User::where('key', $key)->first();
        if (! $model) {
            throw new ModelNotFoundException("User not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }
}
