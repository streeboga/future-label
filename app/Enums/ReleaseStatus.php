<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum ReleaseStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case AwaitingPayment = 'awaiting_payment';
    case AwaitingContract = 'awaiting_contract';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Published = 'published';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Черновик',
            self::AwaitingPayment => 'Ожидает оплаты',
            self::AwaitingContract => 'Ожидает договора',
            self::InReview => 'На проверке',
            self::Approved => 'Одобрен',
            self::Published => 'Опубликован',
            self::Rejected => 'Отклонён',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::AwaitingPayment => 'warning',
            self::AwaitingContract => 'warning',
            self::InReview => 'info',
            self::Approved => 'success',
            self::Published => 'success',
            self::Rejected => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::AwaitingPayment => 'heroicon-o-credit-card',
            self::AwaitingContract => 'heroicon-o-document-text',
            self::InReview => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-circle',
            self::Published => 'heroicon-o-globe-alt',
            self::Rejected => 'heroicon-o-x-circle',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
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
            self::Draft => [self::AwaitingPayment, self::AwaitingContract, self::InReview],
            self::AwaitingPayment => [self::InReview],
            self::AwaitingContract => [self::InReview],
            self::InReview => [self::Approved, self::Rejected],
            self::Approved => [self::Published],
            self::Published => [],
            self::Rejected => [self::Draft],
        };
    }
}
