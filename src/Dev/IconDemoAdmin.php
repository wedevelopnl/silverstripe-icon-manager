<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Dev;

use SilverStripe\Admin\ModelAdmin;

/**
 * Dev-only ModelAdmin exposing {@see IconDemoObject}.
 *
 * Hidden from the CMS menu by default; _config/dev.yml re-enables it under an
 * `Only: environment: dev` block so consumers never see it.
 */
class IconDemoAdmin extends ModelAdmin
{
    /** @config */
    private static string $url_segment = 'icon-demo';

    /** @config */
    private static string $menu_title = 'Icon demo';

    /** @config */
    private static bool $ignore_menuitem = true;

    /**
     * @var array<string>
     * @config
     */
    private static array $managed_models = [
        IconDemoObject::class,
    ];
}
