<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Contract;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin Contract
 */
final class ContractResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'template_version' => $this->template_version,
            'pdf_url' => $this->pdf_url,
            'status' => $this->resource->status->value,
            'accepted_at' => $this->accepted_at?->toIso8601String(),
            'accepted_ip' => $this->accepted_ip,
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
            'release' => fn () => ReleaseResource::make($this->whenLoaded('release')),
            'user' => fn () => UserResource::make($this->whenLoaded('user')),
        ];
    }

    public function toId(Request $request): string
    {
        return $this->key;
    }

    public function toType(Request $request): string
    {
        return 'contracts';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self("/api/v1/contracts/{$this->key}"),
        ];
    }
}
