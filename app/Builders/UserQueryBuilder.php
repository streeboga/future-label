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

    public function byEmail(string $email): self
    {
        $this->query->where('email', mb_strtolower($email));

        return $this;
    }

    public function search(string $term): self
    {
        $this->query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
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
