<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;

final class TrackQueryBuilder
{
    /**
     * @param  Builder<Track>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(Track::query());
    }

    /**
     * @return Builder<Track>
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

    public function byReleaseId(int $releaseId): self
    {
        $this->query->where('release_id', $releaseId);

        return $this;
    }

    public function orderedByTrackNumber(): self
    {
        $this->query->orderBy('track_number');

        return $this;
    }

    public function withRelease(): self
    {
        $this->query->with('release');

        return $this;
    }
}
