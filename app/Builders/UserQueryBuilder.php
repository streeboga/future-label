<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class UserQueryBuilder
{
    /**
     * @param  Builder<User>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(User::query());
    }

    /**
     * @return Builder<User>
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function withRole(UserRole $role): self
    {
        $this->query->where('role', $role->value);

        return $this;
    }

    public function byKey(string $key): self
    {
        $this->query->where('key', $key);

        return $this;
    }

    public function byEmail(string $email): self
    {
        $this->query->where('email', mb_strtolower($email));

        return $this;
    }

    public function search(string $term): self
    {
        $escaped = str_replace(['%', '_'], ['\%', '\_'], $term);

        $this->query->where(function (Builder $q) use ($escaped): void {
            $q->where('name', 'like', "%{$escaped}%")
                ->orWhere('email', 'like', "%{$escaped}%");
        });

        return $this;
    }

    public function sortBy(string $column, string $direction = 'asc'): self
    {
        $allowed = ['created_at', 'updated_at', 'name', 'email'];
        if (in_array($column, $allowed, true)) {
            $this->query->orderBy($column, $direction);
        }

        return $this;
    }
}
