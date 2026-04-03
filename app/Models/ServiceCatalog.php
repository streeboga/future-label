<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceCategory;
use Database\Factories\ServiceCatalogFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $key
 * @property string $title
 * @property string|null $description
 * @property string $price
 * @property string $currency
 * @property ServiceCategory $category
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Order> $orders
 * @property-read Collection<int, Release> $releases
 */
class ServiceCatalog extends Model
{
    /** @use HasFactory<ServiceCatalogFactory> */
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'key',
        'title',
        'description',
        'price',
        'currency',
        'category',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ServiceCategory::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceCatalog $model): void {
            if (empty($model->key)) {
                $model->key = 'svc_'.Str::ulid();
            }
        });
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'service_id');
    }

    /**
     * @return BelongsToMany<Release, $this>
     */
    public function releases(): BelongsToMany
    {
        return $this->belongsToMany(Release::class, 'release_services', 'service_id', 'release_id')
            ->withTimestamps();
    }
}
