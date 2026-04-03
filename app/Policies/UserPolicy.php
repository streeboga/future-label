<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role === UserRole::Admin;
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role === UserRole::Admin;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
