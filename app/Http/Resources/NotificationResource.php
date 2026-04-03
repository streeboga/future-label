<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Notification;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin Notification
 */
final class NotificationResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'type' => $this->resource->type->value,
            'title' => $this->title,
            'body' => $this->body,
            'read_at' => $this->read_at?->toIso8601String(),
            'data' => $this->data,
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
            'user' => fn () => UserResource::make($this->whenLoaded('user')),
        ];
    }

    public function toId(Request $request): string
    {
        return $this->key;
    }

    public function toType(Request $request): string
    {
        return 'notifications';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self("/api/v1/notifications/{$this->key}"),
        ];
    }
}
