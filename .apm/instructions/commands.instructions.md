---
applyTo: '**/*'
---

# Commands

## PHP (via Task — requires Docker)

| Command | Description |
|---------|-------------|
| `task up` / `task down` / `task destroy` / `task build` | Manage Docker services |
| `task ensure-up` | Start services if not already running |
| `task sh` | Open a shell in the app container |
| `task test` | All PHP suites + JS tests (unit + integration + functional + JS) |
| `task test-unit` / `test-integration` / `test-functional` | Individual PHP suites |
| `task test-js` | Vitest |
| `task test-e2e` | Playwright E2E (requires Docker) |
| `task test-e2e-ui` | Playwright E2E with the interactive UI |
| `task coverage` / `task coverage-check` | PHP coverage report / 90% gate |
| `task coverage-js` | JS coverage (Vitest) |
| `task analyse` | PHPStan |
| `task rector` / `task rector-dry` | Rector apply / preview |
| `task mutate` | Infection mutation testing |
| `task flush` / `task dev-build` | Clear cache / dev/build |
| `task qa` | Full QA suite (PHPStan, Rector dry-run, PHP coverage gate, Biome lint/format, tsc, Vitest, Vite build) |
| `task qa-js` | JS-only QA (lint + format + typecheck + test + build) |

## npm

| Command | Description |
|---------|-------------|
| `npm run build` | Vite production build |
| `npm run dev` | Vite watch mode |
| `npm run test` / `npm run test:watch` | Vitest (once / watch) |
| `npm run coverage` | Vitest with coverage |
| `npm run lint` / `lint:fix` | Biome lint (check / autofix) |
| `npm run format` / `format:check` | Biome format (write / check) |
| `npm run typecheck` | `tsc --noEmit` for both the main tsconfig and `tests/E2E` |
| `npm run qa` | lint + format:check + typecheck + test + build |
| `npm run test:e2e` / `test:e2e:ui` / `test:e2e:debug` | Playwright, direct npm entry points |

There is no `task seed-fixture` — this module registers no E2E fixtures; the
Playwright spec seeds its own data through the CMS UI.

Default admin credentials in the testbed: `admin`/`admin`.
