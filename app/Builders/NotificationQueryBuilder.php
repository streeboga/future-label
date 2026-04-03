<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\NotificationType;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;

final class NotificationQueryBuilder
{
    /**
     * @param  Builder<Notification>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(Notification::query());
    }

    /**
     * @return Builder<Notification>
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function byKey(string $key): self
    {
        $this->query->where('key', $key);

        return $this;
    }

    public function byUserId(int $userId): self
    {
        $this->query->where('user_id', $userId);

        return $this;
    }

    public function withType(NotificationType $type): self
    {
        $this->query->where('type', $type->value);

        return $this;
    }

    public function unreadOnly(): self
    {
        $this->query->whereNull('read_at');

        return $this;
    }

    public function readOnly(): self
    {
        $this->query->whereNotNull('read_at');

        return $this;
    }

    public function withUser(): self
    {
        $this->query->with('user');

        return $this;
    }

    public function latest(): self
    {
        $this->query->latest();

        return $this;
    }
}
