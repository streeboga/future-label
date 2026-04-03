<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReleaseStatus;
use App\Enums\ReleaseType;
use Database\Factories\ReleaseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property string $title
 * @property string|null $artist_name
 * @property ReleaseType $type
 * @property string|null $genre
 * @property string|null $language
 * @property string|null $description
 * @property Carbon|null $release_date
 * @property string|null $cover_url
 * @property ReleaseStatus $status
 * @property string|null $reject_reason
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $submitted_at
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Collection<int, Track> $tracks
 * @property-read Collection<int, ServiceCatalog> $services
 */
class Release extends Model
{
    /** @use HasFactory<ReleaseFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'user_id',
        'title',
        'artist_name',
        'type',
        'genre',
        'language',
        'description',
        'release_date',
        'cover_url',
        'status',
        'reject_reason',
        'metadata',
        'submitted_at',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ReleaseType::class,
            'status' => ReleaseStatus::class,
            'metadata' => 'array',
            'release_date' => 'date',
            'submitted_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected static function booted(): void
    {
        static::creating(function (Release $model): void {
            if (empty($model->key)) {
                $model->key = 'rel_'.Str::ulid();
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
     * @return HasMany<Track, $this>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }

    /**
     * @return BelongsToMany<ServiceCatalog, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCatalog::class, 'release_services', 'release_id', 'service_id')
            ->withTimestamps();
    }
}
