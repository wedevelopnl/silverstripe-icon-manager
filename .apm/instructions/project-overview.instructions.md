---
applyTo: '**/*'
---

# Project Overview

SilverStripe Icon Manager — upload and manage custom SVG icons, and select them
through an `IconDropdownField` with a live preview.

Package: `wedevelopnl/silverstripe-icon-manager` (type: `silverstripe-vendormodule`)

## Requirements

- PHP ^8.3
- `silverstripe/framework` ^6.0, `silverstripe/admin` ^3.0, `silverstripe/asset-admin` ^3.0,
  `silverstripe/vendor-plugin` ^3.0
- `wedevelopnl/silverstripe-svg-image` ^6.0@dev — provides the `Svg` file class,
  which sanitises SVG content on write
- Does **not** require `silverstripe/cms`; nothing in `src/` touches `SiteTree`
