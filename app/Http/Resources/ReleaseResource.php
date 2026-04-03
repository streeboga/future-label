<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Release;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin Release
 */
final class ReleaseResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'artist_name' => $this->artist_name,
            'type' => $this->resource->type->value,
            'genre' => $this->genre,
            'language' => $this->language,
            'description' => $this->description,
            'release_date' => $this->release_date?->toDateString(),
            'cover_url' => $this->cover_url,
            'status' => $this->resource->status->value,
            'reject_reason' => $this->reject_reason,
            'metadata' => $this->metadata,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<int|string, mixed>
     */
    public function toRelationships(Request $request): array
    {
        return [
            'tracks' => fn () => TrackResource::collection($this->whenLoaded('tracks')),
            'user' => fn () => UserResource::make($this->whenLoaded('user')),
            'contracts' => fn () => ContractResource::collection($this->whenLoaded('contracts')),
        ];
    }

    public function toId(Request $request): string
    {
        return $this->key;
    }

    public function toType(Request $request): string
    {
        return 'releases';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self("/api/v1/releases/{$this->key}"),
        ];
    }
}
