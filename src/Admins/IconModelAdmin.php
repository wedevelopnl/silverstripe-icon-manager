<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Admins;

use SilverStripe\Admin\ModelAdmin;
use WeDevelop\IconManager\Models\Icon;

class IconModelAdmin extends ModelAdmin
{
    /** @config */
    private static string $url_segment = 'icons';

    /** @config */
    private static string $menu_title = 'Custom icons';

    /** @config */
    private static string $menu_icon_class = 'font-icon-pencil';

    /**
     * @var array<string>
     * @config
     */
    private static array $managed_models = [
        Icon::class,
    ];
}
