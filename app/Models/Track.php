<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TrackFormat;
use Database\Factories\TrackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $key
 * @property int $release_id
 * @property string $title
 * @property int $track_number
 * @property int|null $duration_seconds
 * @property string|null $file_url
 * @property TrackFormat $format
 * @property int|null $file_size
 * @property string|null $authors
 * @property string|null $composers
 * @property string|null $lyrics
 * @property string|null $isrc
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Release $release
 */
class Track extends Model
{
    /** @use HasFactory<TrackFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'release_id',
        'title',
        'track_number',
        'duration_seconds',
        'file_url',
        'format',
        'file_size',
        'authors',
        'composers',
        'lyrics',
        'isrc',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'format' => TrackFormat::class,
            'track_number' => 'integer',
            'duration_seconds' => 'integer',
            'file_size' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected static function booted(): void
    {
        static::creating(function (Track $model): void {
            if (empty($model->key)) {
                $model->key = 'trk_'.Str::ulid();
            }
        });
    }

    /**
     * @return BelongsTo<Release, $this>
     */
    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }
}
