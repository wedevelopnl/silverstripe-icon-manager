---
applyTo: '**/*.ts'
---

# TypeScript Conventions

- Vanilla TypeScript — no framework. No jQuery.
- `@` path alias maps to `client/src/js`.
- Entry point: `client/src/js/bundle.ts` → `client/dist/js/bundle.js` (IIFE).
- Tests co-located as `*.test.ts`, run under Vitest with jsdom.
- Biome enforces `noNonNullAssertion` in production code; it is relaxed in tests.
- CMS forms load over Pjax, so DOM wiring uses a `MutationObserver`, never
  `DOMContentLoaded` alone and never entwine `onmatch`.
