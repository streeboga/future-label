<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ServiceCatalog;
use App\Models\User;

final class ServiceCatalogPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ServiceCatalog $service): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Manager], true);
    }

    public function update(User $user, ServiceCatalog $service): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Manager], true);
    }

    public function delete(User $user, ServiceCatalog $service): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Manager], true);
    }
}
