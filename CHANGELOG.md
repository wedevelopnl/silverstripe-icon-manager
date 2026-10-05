# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
The major version tracks the SilverStripe major.

## [Unreleased]

### Added

- Dutch translation (`lang/nl.yml`).
- Translatable `Icons` menu title, `Icon` field labels and the grid's `Preview`
  column header.

### Changed

- `IconDropdownField`'s `$title` now defaults to `null`, which resolves to the
  translated `Icon` model name instead of a hardcoded English `'Icon'`.

### Fixed

- The `Icon` model names are now actually translated: `lang/en.yml` used the
  keys `SINGULAR_NAME`/`PLURAL_NAME`, which SilverStripe never reads
  (`SINGULARNAME`/`PLURALNAME`).

## [6.0.0] - 2026-10-01

First release for SilverStripe 6. There is no 5.x. See the
[updating guide](docs/updating.md#41---600) for upgrade steps.

### Added

- Translatable strings for the `IconDropdownField` preview messages and the
  `Icon` model names (`lang/en.yml`).
- The icon preview caches fetched icons for the CMS session, so switching back
  to an icon does not request it again.

### Changed

- Requires PHP ^8.3, `silverstripe/framework` ^6.0, `silverstripe/admin` ^3.0,
  `silverstripe/asset-admin` ^3.0 and `wedevelopnl/silverstripe-svg-image` ^6.0.
- No longer requires `silverstripe/cms`; the module works in admin-only installs.
- The preview script is rewritten in plain TypeScript and no longer depends on
  jQuery.
- Built assets moved: `client/dist/icondropdownfield.js` is now
  `client/dist/js/bundle.js`, and `client/dist/silverstripe-icon-manager.css` is
  now `client/dist/styles/bundle.css`. Update any custom requirements that point
  at the old paths.
- `IconDropdownField` loads its script when constructed instead of in `Field()`,
  and sets the preview endpoint through `getAttributes()`.
- The `Icon` table name is now pinned explicitly to
  `WeDevelop_IconManager_Models_Icon`, the name it already had. No database
  migration is needed.
- The Icons admin eager-loads icon files, removing one query per grid row.

### Fixed

- The live preview now works on forms loaded over Pjax. It was previously bound
  once on page load only.
- Each `IconDropdownField` updates its own preview. Previously a change updated
  every icon preview on the page.
- Changing an icon now marks the CMS form as changed, so editors are warned
  before leaving with an unsaved icon selection.
- Non-SVG icon files (such as PNG) preview as an `<img>` tag instead of raw file
  bytes.
- `IconDropdownField` no longer errors when its saved icon has been deleted.

### Removed

- `MigrateToNewIconModelTask` (the 1.0.x to 2.0.x migration). Upgrade to 4.1 and
  run it there first if you are still on 1.0.x.
- `Icon::forTemplate()`, deprecated since 2.0.1. Use `$Icon.Icon.Tag` in
  templates, or `$icon->Icon()->getTag()` in PHP.
- `Icon::createFromOldDataset()`, an internal helper for the removed migration
  task.

[6.0.0]: https://github.com/wedevelopnl/silverstripe-icon-manager/releases/tag/6.0.0
