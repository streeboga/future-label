<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin Order
 */
final class OrderResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'status' => $this->resource->status->value,
            'notes' => $this->notes,
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
            'service' => fn () => ServiceCatalogResource::make($this->resource->service),
            'user' => fn () => UserResource::make($this->resource->user),
        ];
    }

    public function toId(Request $request): string
    {
        return $this->key;
    }

    public function toType(Request $request): string
    {
        return 'orders';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self("/api/v1/orders/{$this->key}"),
        ];
    }
}
