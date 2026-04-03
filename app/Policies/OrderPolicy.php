<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;

final class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id
            || in_array($user->role, [UserRole::Admin, UserRole::Manager], true);
    }

    public function create(User $user): bool
    {
        return true;
    }
}
