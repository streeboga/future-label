# Backend — Laravel 13 API

## Stack

- **Laravel 13** + **PHP 8.4**
- **Auth**: Laravel Sanctum (Bearer Token, stateless)
- **API format**: JSON:API v1.1 via `timacdonald/json-api`
- **DTOs**: `spatie/laravel-data`
- **Filtering/sorting**: `spatie/laravel-query-builder`
- **Money**: `brick/money`
- **API docs**: `dedoc/scramble` (auto OpenAPI)
- **DB**: PostgreSQL (`future_label`)

## Directory Structure

```
app/
├── Builders/                 # QueryBuilder — все фильтры, сортировки, eager loading
├── Contracts/
│   ├── Enums/                # Enum interfaces: HasLabel, HasColor, HasIcon
│   └── Repositories/         # Repository interfaces
├── DataTransferObjects/      # Spatie Data DTOs
│   └── {Entity}/
│       ├── Create{Entity}Data.php
│       └── Update{Entity}Data.php
├── Enums/                    # PHP Enums (статусы, типы, константы)
├── Http/
│   ├── Controllers/Api/V1/  # Thin controllers → только Service
│   ├── Middleware/
│   ├── Requests/{Entity}/   # FormRequests с toDto()
│   └── Resources/           # JsonApiResource subclasses
├── Models/                   # Eloquent: relations, casts, accessors. БЕЗ scopeXxx()
├── Providers/
├── Repositories/
│   └── Eloquent/            # Repository implementations
└── Services/                 # Business logic, transactions, events
```

## Layer Rules

```
Controller → Service ONLY
Service    → Repository (never Model::query() directly)
Repository → QueryBuilder + Model
```

## Key Commands

```bash
php artisan serve
php artisan migrate
php artisan route:list --path=api
./vendor/bin/phpstan analyse
./vendor/bin/pint
php artisan test
```

## API Routes

- Base: `/api/v1/`
- Auth: Bearer Token via Sanctum
- Format: `application/vnd.api+json`
- Update: PATCH (not PUT)
- Create: 201 + Location header
- Delete: 204 No Content
