<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\ReleaseStatus;
use App\Enums\ReleaseType;
use App\Models\Release;
use Illuminate\Database\Eloquent\Builder;

final class ReleaseQueryBuilder
{
    /**
     * @param  Builder<Release>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(Release::query());
    }

    /**
     * @return Builder<Release>
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

    public function withStatus(ReleaseStatus $status): self
    {
        $this->query->where('status', $status->value);

        return $this;
    }

    public function withType(ReleaseType $type): self
    {
        $this->query->where('type', $type->value);

        return $this;
    }

    public function search(string $term): self
    {
        $escaped = str_replace(['%', '_'], ['\%', '\_'], $term);

        $this->query->where(function (Builder $q) use ($escaped): void {
            $q->where('title', 'like', "%{$escaped}%")
                ->orWhere('artist_name', 'like', "%{$escaped}%");
        });

        return $this;
    }

    public function sortBy(string $column, string $direction = 'asc'): self
    {
        $allowed = ['created_at', 'updated_at', 'title', 'release_date'];
        if (in_array($column, $allowed, true)) {
            $this->query->orderBy($column, $direction);
        }

        return $this;
    }

    public function withTracks(): self
    {
        $this->query->with('tracks');

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
