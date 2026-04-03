<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Contract;
use App\Models\User;

final class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id
            || $user->role === UserRole::Admin
            || $user->role === UserRole::Manager;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function accept(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id;
    }

    public function download(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id
            || $user->role === UserRole::Admin
            || $user->role === UserRole::Manager;
    }
}
