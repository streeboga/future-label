# Tests — Pest 4

## Stack

- **Framework**: Pest v4.4 + `pest-plugin-laravel` v4.1
- **Coverage**: Xdebug (`XDEBUG_MODE=coverage`)
- **Target**: >= 85% coverage

## Structure

```
tests/
├── Feature/       # HTTP/API integration tests (extend ApiTestCase)
│   └── Api/V1/   # per-entity test files
├── Unit/          # Pure unit tests (services, helpers)
└── Pest.php       # Global helpers, uses(), datasets
```

## Key Commands

```bash
./vendor/bin/pest                          # run all tests
./vendor/bin/pest --filter=UserTest        # run specific test
./vendor/bin/pest --parallel               # parallel execution
XDEBUG_MODE=coverage ./vendor/bin/pest --coverage --min=85
./vendor/bin/pest --mutate                 # mutation testing
```

## Rules

- Every test file must have `covers()` or `mutates()` attribute
- Feature tests hit a real test database (no mocks on DB layer)
- Use `RefreshDatabase` trait in feature tests
- Factories for all test data — no manual `DB::insert()`
- Test all JSON:API response structure: `data.type`, `data.id`, `data.attributes`

## Test Database

Uses the same PostgreSQL connection with `DB_DATABASE=future_label_test` (set in `phpunit.xml`).
