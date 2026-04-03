# Database

## Connection

- **Driver**: PostgreSQL
- **Database**: `future_label`
- **User**: `k.mazurov`
- **Host**: `127.0.0.1:5432`

## Conventions

- Every entity table has a `key` column: `string, 40 chars, unique` (prefix + ULID)
- Primary key: auto-increment `id` (internal), `key` is the public identifier
- `getRouteKeyName()` on models returns `'key'`
- Timestamps: always `created_at` / `updated_at`
- Soft deletes: add `deleted_at` only when needed

## Key Commands

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan migrate:rollback
php artisan make:migration create_{table}_table
php artisan make:seeder {Entity}Seeder
php artisan make:factory {Entity}Factory
```

## Current Migrations

| Migration | Table |
|-----------|-------|
| `0001_01_01_000000` | `users`, `password_reset_tokens`, `sessions` |
| `0001_01_01_000001` | `cache`, `cache_locks` |
| `0001_01_01_000002` | `jobs`, `job_batches`, `failed_jobs` |
