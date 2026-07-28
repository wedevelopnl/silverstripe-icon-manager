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

Icons are rendered with SilverStripe's `getTag()`, so each file type renders as
its natural HTML element — SVGs inline as `<svg>`, raster images as `<img>`. Any
file type SilverStripe can render is safe to add here.

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
```

## Rendering icon in template

You can simply render a icon by using the `$Icon` (or the name of your relation) property in the `.ss` template.

`$Icon` renders the icon via its file's `getTag()`. For SVG icons, wrap the
output in a `<span>` with your own classes to control size and colour.
