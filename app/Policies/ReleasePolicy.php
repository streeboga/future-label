<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Release;
use App\Models\User;

final class ReleasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Release $release): bool
    {
        return $user->id === $release->user_id
            || $user->role === UserRole::Admin
            || $user->role === UserRole::Manager;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Release $release): bool
    {
        return $user->id === $release->user_id
            || $user->role === UserRole::Admin;
    }

    public function delete(User $user, Release $release): bool
    {
        return $user->id === $release->user_id
            || $user->role === UserRole::Admin;
    }

    public function submit(User $user, Release $release): bool
    {
        return $user->id === $release->user_id;
    }
}
