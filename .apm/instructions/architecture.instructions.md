---
applyTo: '**/*'
---

# Architecture

```
_config/              YAML config (admin CSS, icon file category, dev-only gate)
templates/             .ss templates (IconDropdownField holder)
src/Models/            Icon DataObject
src/Forms/             IconDropdownField
src/Admins/            IconModelAdmin
src/Dev/               Dev-only demo object + admin (excluded from the package)
lang/                  i18n strings
client/src/js/         TypeScript source
client/src/styles/     SCSS source
client/dist/           Vite build output (committed, exposed)
tests/Unit/            PHPUnit unit tests (no DB/framework)
tests/Integration/     PHPUnit integration tests (full SS env)
tests/Functional/      PHPUnit functional tests
tests/E2E/             Playwright specs, setup and test assets
.docker/               Docker dev env: FrankenPHP + Caddy + MySQL 8
```

- PSR-4: `WeDevelop\IconManager\` → `src/`; `WeDevelop\IconManager\Tests\` → `tests/`
- The module is developed against a throwaway SS6 app in Docker. The module source is
  bind-mounted at `/module` and installed via a Composer path repo with `symlink: true`.
- Node: >=26 (pinned in `.nvmrc`)
- `.gitattributes` marks `src/Dev`, `tests/`, `docs/`, `client/src`, dev tooling and the
  generated agent files `export-ignore`, so a Composer package archive ships only
  `src/{Models,Forms,Admins}`, `_config` (minus `dev.yml`), `templates`, `lang` and
  `client/dist`.
