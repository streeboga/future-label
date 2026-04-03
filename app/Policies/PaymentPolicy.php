<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;

final class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->id === $payment->user_id
            || $user->role === UserRole::Admin
            || $user->role === UserRole::Manager;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function confirm(User $user, Payment $payment): bool
    {
        return $user->role === UserRole::Admin
            || $user->role === UserRole::Manager;
    }

    public function reject(User $user, Payment $payment): bool
    {
        return $user->role === UserRole::Admin
            || $user->role === UserRole::Manager;
    }
}
