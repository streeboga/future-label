<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationType;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property NotificationType $type
 * @property string $title
 * @property string $body
 * @property Carbon|null $read_at
 * @property array<string, mixed>|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'user_id',
        'type',
        'title',
        'body',
        'read_at',
        'data',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => NotificationType::class,
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected static function booted(): void
    {
        static::creating(function (Notification $model): void {
            if (empty($model->key)) {
                $model->key = 'ntf_'.Str::ulid();
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
}
