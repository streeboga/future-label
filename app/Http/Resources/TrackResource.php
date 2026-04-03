<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Track;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin Track
 */
final class TrackResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'track_number' => $this->track_number,
            'duration_seconds' => $this->duration_seconds,
            'file_url' => $this->file_url,
            'format' => $this->resource->format->value,
            'file_size' => $this->file_size,
            'authors' => $this->authors,
            'composers' => $this->composers,
            'lyrics' => $this->lyrics,
            'isrc' => $this->isrc,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<int|string, mixed>
     */
    public function toRelationships(Request $request): array
    {
        return [];
    }

    public function toId(Request $request): string
    {
        return $this->key;
    }

    public function toType(Request $request): string
    {
        return 'tracks';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self("/api/v1/releases/{$this->resource->release->key}/tracks/{$this->key}"),
        ];
    }
}
