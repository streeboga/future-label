<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ServiceCatalog;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin ServiceCatalog
 */
final class ServiceCatalogResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'category' => $this->resource->category->value,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
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
        return 'services';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self("/api/v1/services/{$this->key}"),
        ];
    }
}
