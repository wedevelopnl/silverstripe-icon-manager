# Updating guides

## 4.1 - 6.0.0

Version 6.0.0 targets SilverStripe 6 and PHP 8.3+. The major version now tracks
the SilverStripe major, so there is no 5.x.

### No database changes

The `Icon` table keeps the name it has always had,
`WeDevelop_IconManager_Models_Icon`. It is now pinned explicitly on the model so
a future namespace move cannot rename it underneath you, but the value is
unchanged — run `dev/build` as usual and your records are where they were.

### Removed APIs

* `MigrateToNewIconModelTask` (the 1.0.x → 2.0.x migration) has been removed. If
  you are still on 1.0.x, upgrade to 4.1 first and run that task there.
* `Icon::forTemplate()`, deprecated since 2.0.1, has been removed. Call
  `$Icon.Icon.Tag` in templates, or `$icon->Icon()->getTag()` in PHP.

### SVG support

`wedevelopnl/silverstripe-svg-image` must be on its 6.x line. It is pulled in
automatically by Composer.

## 2.0.2 - 3.0.0
Since 3.0.0 this module utilises the `wedevelopnl/silverstripe-svg-image` module
for SVG support. Inorder to update to `3.0.0` you'll have to migrate any existing
SVG icons into the new object class. Inorder to perform this update run the 
migration task shipped with the svg image module.

```shell
vendor/bin/sake dev/tasks/migrate-svg-files
```
