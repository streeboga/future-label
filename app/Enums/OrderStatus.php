<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Paid = 'paid';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает оплаты',
            self::Paid => 'Оплачен',
            self::InProgress => 'В работе',
            self::Completed => 'Завершён',
            self::Cancelled => 'Отменён',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'info',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-credit-card',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Completed => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
