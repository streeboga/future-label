<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Payment;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin Payment
 */
final class PaymentResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'method' => $this->resource->method->value,
            'status' => $this->resource->status->value,
            'provider' => $this->provider,
            'provider_payment_id' => $this->provider_payment_id,
            'receipt_url' => $this->receipt_url,
            'confirmed_at' => $this->confirmed_at?->toIso8601String(),
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
        return 'payments';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self("/api/v1/payments/{$this->key}"),
        ];
    }
}
