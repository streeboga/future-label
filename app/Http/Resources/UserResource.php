<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

/**
 * @mixin User
 */
final class UserResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->resource->role->value,
            'stage_name' => $this->stage_name,
            'phone' => $this->phone,
            'telegram' => $this->telegram,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];

        if ($this->canViewPii($request)) {
            $attributes['passport_data'] = $this->passport_data;
            $attributes['bank_details'] = $this->bank_details;
        }

        return $attributes;
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
        return 'users';
    }

    /**
     * @return array<int, Link>
     */
    public function toLinks(Request $request): array
    {
        return [
            Link::self('/api/v1/profile'),
        ];
    }

    private function canViewPii(Request $request): bool
    {
        $authUser = $request->user();

        if (! $authUser instanceof User) {
            return false;
        }

        return $authUser->id === $this->resource->id
            || $authUser->role === UserRole::Admin;
    }
}
