---
applyTo: '**/*'
---

# Gotchas

- **`File::getString()` returns raw bytes, `getTag()` returns markup.** Preview
  code must always use `getTag()`; `getString()` renders binary data into the DOM
  for any non-SVG icon file type.
- **`FormField::Link()` throws when the field has no form.** Guard with
  `$this->getForm() !== null` before calling it from `getAttributes()`.
- **`client/dist` is a committed build artifact.** CI fails if it drifts from
  source. Run `npm run build` and commit the output.
- **This module registers no E2E fixtures, deliberately.** `FixtureLoader` requires
  every fixture to create a `SiteTree` record, and its `reset()` only archives
  pages — non-page DataObjects stack up across runs. The E2E surface here is a
  `ModelAdmin` with no page, so the spec seeds itself through the CMS UI with a
  per-run unique title. The `silverstripe-e2e` dependency stays for
  `authenticateAdmin` and for the `Session.strict_user_agent_check: false` its own
  config applies.
- **The e2e module's `attach_image` post-action hardcodes `Image::create()`**, so
  it cannot produce an `Svg` record either way.
- **`@wedevelop/e2e` is a tsconfig `paths` alias, not an npm package** — the
  package is `private: true` with no `main`/`exports`. A host `composer install`
  must run before `npm run typecheck` or the alias target does not exist.
- **`wedevelopnl/silverstripe-e2e` is versioned in two places** — `composer.json`
  (host vendor, used by the Playwright client) and `.docker/app/composer.json`
  (container vendor). Bump them together.
- **Docker `exec` needs `-T` in non-TTY shells.** Override per invocation rather
  than editing `Taskfile.yml`.
- **`_config/dev.yml`'s `Only: environment: dev` gate is not covered by an
  automated test.** SilverStripe's `TestKernel` forces `Kernel::getEnvironment()`
  to `dev` for every PHPUnit run, so the override is unconditionally active inside
  the test suite and no `Config`-based assertion can ever observe the non-dev
  behaviour. `tests/Integration/Dev/IconDemoAdminTest.php` therefore asserts
  `IconDemoAdmin`'s declared `ignore_menuitem` default via `ReflectionProperty`
  instead of `Config::inst()`. Do not assume the YAML gate itself is tested.
- **CLAUDE.md / AGENTS.md / GEMINI.md are generated** from `.apm/instructions/` on
  every `apm compile`. Edit the sources, never the generated files.
