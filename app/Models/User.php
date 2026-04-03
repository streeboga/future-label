<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $stage_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $passport_data
 * @property string|null $bank_details
 * @property UserRole $role
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'key',
        'name',
        'stage_name',
        'email',
        'phone',
        'telegram',
        'passport_data',
        'bank_details',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'passport_data',
        'bank_details',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'passport_data' => 'encrypted',
            'bank_details' => 'encrypted',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected static function booted(): void
    {
        static::creating(function (User $model): void {
            if (empty($model->key)) {
                $model->key = 'usr_'.Str::ulid();
            }
        });
    }

    /**
     * @return HasMany<Release, $this>
     */
    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }
}
