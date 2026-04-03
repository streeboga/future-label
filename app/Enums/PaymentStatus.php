<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum PaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Paid = 'paid';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Ожидание',
            self::Processing => 'Обработка',
            self::Paid => 'Оплачен',
            self::Confirmed => 'Подтверждён',
            self::Failed => 'Ошибка',
            self::Refunded => 'Возвращён',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Paid => 'success',
            self::Confirmed => 'success',
            self::Failed => 'danger',
            self::Refunded => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Processing => 'heroicon-o-arrow-path',
            self::Paid => 'heroicon-o-check-circle',
            self::Confirmed => 'heroicon-o-shield-check',
            self::Failed => 'heroicon-o-x-circle',
            self::Refunded => 'heroicon-o-receipt-refund',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Processing, self::Paid, self::Confirmed, self::Failed],
            self::Processing => [self::Paid, self::Failed],
            self::Paid => [self::Confirmed, self::Refunded],
            self::Confirmed => [self::Refunded],
            self::Failed => [self::Pending],
            self::Refunded => [],
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Refunded], true);
    }

    public function isActive(): bool
    {
        return ! $this->isTerminal();
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case): string => $case->getLabel(), self::cases())
        );
    }
}
