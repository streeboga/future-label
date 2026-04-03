<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Notification;
use App\Models\User;

final class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id
            || $user->role === UserRole::Admin;
    }

    public function markAsRead(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }
}
