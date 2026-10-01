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
  pages — non-page DataObjects stack up across runs, and every journey starts
  with an `Icon` record. Specs seed themselves through the CMS UI with per-run
  unique titles (`tests/E2E/support/icons.ts`). The `silverstripe-e2e` dependency stays for
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
  `IconDemoAdmin`'s declared `ignore_menuitem` default and its absent
  `url_segment` via reflection instead of `Config::inst()`. Do not assume the
  YAML gate itself is tested.
- **`ignore_menuitem` only hides the CMS menu entry, not the route.**
  `AdminRootController::rules()` builds routes from every `url_segment` it finds
  via `CMSMenu::get_cms_classes()`, which does not filter on `ignore_menuitem`.
  `IconDemoAdmin` declares no `url_segment` of its own for this reason —
  `add_rule_for_controller()` skips any controller whose `url_segment` config is
  empty — so `_config/dev.yml` is what keeps `/admin/icon-demo` out of prod, not
  `ignore_menuitem`.
- **The testbed project (`.docker/app/{_config,src,templates}`) mirrors
  `docs/configuration.md` verbatim** — png in `wedevelop/icon`, the `Page` with a
  `has_one` Icon, `$Icon.Icon.Tag` in `Page.ss`. Change docs and testbed together.
  Bind-mounted into `/app`, never `/module`, so no duplicate `Page` class.
- **Testbed `Page::getCMSFields()` stays untyped** — `ErrorPage extends Page` with
  an untyped override; a return type is a fatal error on container start.
- **Testbed `Page.ss` must render `$Content` and `$Form`** — `Security` renders the
  frontend login form through it; without them `authenticateAdmin` times out.
- **A new page keeps its `new-page-N` URL segment** after the title changes
  (regenerated only while it equals bare `new-page`). E2E reads the segment back
  from `input[name="URLSegment"]`; never derive it from the title.
- **CLAUDE.md / AGENTS.md / GEMINI.md are generated** from `.apm/instructions/` on
  every `apm compile`. Edit the sources, never the generated files.
