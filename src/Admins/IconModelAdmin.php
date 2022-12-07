<?php

namespace TheWebmen\IconManager\Admins;

use SilverStripe\Admin\ModelAdmin;
use TheWebmen\IconManager\Models\Icon;

class IconModelAdmin extends ModelAdmin
{
    /**
     * @var string
     * @config
     */
    private static $url_segment = 'icons';

    /**
     * @var string
     * @config
     */
    private static $menu_title = 'Custom icons';

    /**
     * @var string
     * @config
     */
    private static $menu_icon_class = 'font-icon-pencil';

    /**
     * @var array
     * @config
     */
    private static $managed_models = [
        Icon::class,
    ];
}
