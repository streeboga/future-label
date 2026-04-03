# Docs

Project documentation and planning artifacts.

## Structure

```
docs/
└── plans/     # Design docs and implementation plans (YYYY-MM-DD-<topic>-design.md)
```

## BMAD Output

Planning and implementation artifacts are stored in `_bmad-output/`:
- `_bmad-output/planning-artifacts/` — PRDs, architecture docs, epics
- `_bmad-output/implementation-artifacts/` — stories, sprint plans

## API Docs

Auto-generated OpenAPI docs via Scramble:
```bash
php artisan scramble:export   # export openapi.json
```

Available at `/docs/api` in local dev.
