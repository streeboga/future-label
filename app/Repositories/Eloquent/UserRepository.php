<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\UserQueryBuilder;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class UserRepository implements UserRepositoryInterface
{
    public function findByKey(string $key): User
    {
        $model = UserQueryBuilder::make()
            ->byKey($key)
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("User not found with key [{$key}].");
        }

        return $model;
    }

    public function findByEmail(string $email): ?User
    {
        return UserQueryBuilder::make()
            ->byEmail($email)
            ->getQuery()
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => $password,
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->refresh();
    }
}
