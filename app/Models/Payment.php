<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property int|null $release_id
 * @property int|null $order_id
 * @property string $amount
 * @property string $currency
 * @property PaymentMethod $method
 * @property PaymentStatus $status
 * @property string|null $provider
 * @property string|null $provider_payment_id
 * @property string|null $receipt_url
 * @property int|null $confirmed_by
 * @property Carbon|null $confirmed_at
 * @property array<string, mixed>|null $provider_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Release|null $release
 * @property-read Order|null $order
 * @property-read User|null $confirmedByUser
 */
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'user_id',
        'release_id',
        'order_id',
        'amount',
        'currency',
        'method',
        'status',
        'provider',
        'provider_payment_id',
        'receipt_url',
        'confirmed_by',
        'confirmed_at',
        'provider_data',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'provider_data' => 'array',
            'confirmed_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $model): void {
            if (empty($model->key)) {
                $model->key = 'pay_'.Str::ulid();
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Release, $this>
     */
    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function confirmedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
