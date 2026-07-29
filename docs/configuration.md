# Configuration

## Icon image types
By default the icon manager only accepts SVG files to be used as icon. However you can add
additional image file types _(as long as they are configured to be allowed by Silverstripe)_
by adding the following configuration to your `config.yml`.
```yml
---
Name: app-silverstripeiconmanager
After: '#silverstripeiconmanager-assetsfiletypes'
---

SilverStripe\Assets\File:
  app_categories:
    'wedevelop/icon':
      - png
```
In this example we allow PNGs to be used as icon.

Icons render through `getTag()`, so the file's own class decides the markup.
Raster types render as `<img>`; SVGs render inline and are sanitised on write
by `wedevelopnl/silverstripe-svg-image`. Only add extensions whose `File`
class renders a tag rather than raw file content.

## Using icons with a DataModel/Page
To use a Icon , you can just set a `$has_one` or `$has_many` relation to the Icon class;

```php
$has_one = [ 'Icon' => Icon::class ]
```

There is a `IconDropdownField` FormField that can used as following;

`IconDropdownField::create('IconID', 'Icon')`

Simple complete example to use a icon for every page;

```php
<?php

use SilverStripe\CMS\Model\SiteTree;
use WeDevelop\IconManager\Forms\IconDropdownField;
use WeDevelop\IconManager\Models\Icon;

class Page extends SiteTree
{
    private static $has_one = [
        'Icon' => Icon::class,
    ];

    private static $owns = [
        'Icon',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            IconDropdownField::create('IconID', 'Icon'),
        ]);

        return $fields;
    }
}
```

## Rendering icon in template

How you render the icon in a `.ss` template depends on what your relation points to.

If your `$has_one` points directly at a `File` (or `Svg`) — not through this
module's `Icon` model — then the bare relation name renders it, because
`File::forTemplate()` calls `getTag()` for you.

The example above instead points at the `Icon` wrapper model
(`'Icon' => Icon::class`). That model does not override `forTemplate()`, so a
bare `$Icon` tries to resolve a template for the `Icon` class, and none ships
with this module. Reach through to the file explicitly instead:

`$Icon.Icon.Tag`

The first `Icon` is your relation name; the second is the wrapper model's own
`Icon` has_one to the underlying `File`; `Tag` calls its `getTag()`. For SVG
icons, wrap the output in a `<span>` with your own classes to control size and
colour.
