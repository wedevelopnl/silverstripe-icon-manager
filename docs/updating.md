# Updating guides

## 2.0.1 - 2.0.2
Since 2.0.2 this module utilises the `wedevelopnl/silverstripe-svg-image` module
for SVG support. Inorder to update to `2.0.2` you'll have to migrate any existing
SVG icons into the new object class. Inorder to perform this update run the 
migration task shipped with the svg image module.

```shell
vendor/bin/sake dev/tasks/migrate-svg-files
```