<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Dev;

use SilverStripe\Admin\ModelAdmin;

/**
 * Dev-only ModelAdmin exposing {@see IconDemoObject}.
 *
 * No `url_segment` is declared here, so `AdminRootController` registers no
 * route for it outside dev: `add_rule_for_controller()` skips any admin
 * controller whose `url_segment` config is empty. `_config/dev.yml` sets
 * `url_segment` (and un-hides the CMS menu item) only under an
 * `Only: environment: dev` block. `ignore_menuitem` alone hides only the CMS
 * menu entry, not the route — it is not a security boundary by itself.
 */
class IconDemoAdmin extends ModelAdmin
{
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
