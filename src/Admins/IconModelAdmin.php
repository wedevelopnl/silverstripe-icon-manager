<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Admins;

use Override;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use WeDevelop\IconManager\Models\Icon;

class IconModelAdmin extends ModelAdmin
{
    /** @config */
    private static string $url_segment = 'icons';

    /** @config */
    private static string $menu_title = 'Icons';

    /** @config */
    private static string $menu_icon_class = 'font-icon-pencil';

    /**
     * @var array<string>
     * @config
     */
    private static array $managed_models = [
        Icon::class,
    ];

    /**
     * Icon::$summary_fields renders getPreview(), which reads the has_one File.
     * Without eager loading the grid issues one File query per row.
     *
     * @return DataList<DataObject> Matches the parent — ModelAdmin builds the
     *                              list from $managed_models at runtime, so the
     *                              element type is not statically Icon.
     */
    #[Override]
    public function getList(): DataList
    {
        return parent::getList()->eagerLoad('Icon');
    }
}
