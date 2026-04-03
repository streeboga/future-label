# Frontend — React SPA

## Stack

- **React 19** + **TypeScript** (strict)
- **Build**: Vite + `@tailwindcss/vite`
- **Routing**: `@tanstack/react-router`
- **Data fetching**: `@tanstack/react-query`
- **HTTP client**: `axios` (Bearer Token → `Authorization: Bearer <token>`)
- **UI**: ShadCN (New York style, Framer theme — bold black + electric blue)
- **Icons**: `lucide-react`

## Directory Structure

```
frontend/src/
├── components/
│   └── ui/          # ShadCN components (button, card, input, label, badge)
├── hooks/           # Custom React hooks
├── lib/
│   └── utils.ts     # cn() helper (clsx + tailwind-merge)
├── routes/          # TanStack Router route files
├── services/        # API call functions (axios wrappers)
├── types/           # TypeScript types/interfaces
├── App.tsx
└── main.tsx
```

## Theme (Framer)

- Light: `--background: oklch(0.98 0 0)`, primary black `oklch(0.06 0 0)`
- Dark: `--background: oklch(0.09 0 0)`, primary white
- Accent: electric blue `oklch(0.55 0.22 260)`
- Border radius: `0.25rem` (sharp)

## Adding ShadCN Components

```bash
npx shadcn@latest add <component>
```

Components auto-install to `src/components/ui/`.

## Key Commands

```bash
npm run dev       # dev server (Vite)
npm run build     # production build
npm run lint      # ESLint
npx tsc --noEmit  # TypeScript check
```

## API Integration

All requests go to `/api/v1/` with:
```
Accept: application/vnd.api+json
Authorization: Bearer <token>
Content-Type: application/vnd.api+json
```

Store token in `localStorage` or React context. Use `@tanstack/react-query` for all server state.
