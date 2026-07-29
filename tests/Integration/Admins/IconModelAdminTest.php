<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tests\Integration\Admins;

use ReflectionProperty;
use SilverStripe\Assets\File;
use SilverStripe\Dev\SapphireTest;
use WeDevelop\IconManager\Admins\IconModelAdmin;
use WeDevelop\IconManager\Models\Icon;

class IconModelAdminTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testGetListEagerLoadsTheIconFile(): void
    {
        $file = File::create();
        $file->setFromString('<svg xmlns="http://www.w3.org/2000/svg"></svg>', 'Icons/star.svg');
        $file->write();

        $icon = Icon::create(['Title' => 'Star']);
        $icon->IconID = $file->ID;
        $icon->write();

        // ModelAdmin has no setter for $modelClass — it is only populated by
        // ModelAdmin::init(), which needs a full CMS request and permission
        // cycle. Reflection is the cheap way to reach getList() directly.
        $admin = IconModelAdmin::create();
        $modelClass = new ReflectionProperty($admin, 'modelClass');
        $modelClass->setValue($admin, Icon::class);

        // eagerLoad() throws on an unknown relation name, so a list that still
        // yields the icon with its file attached is what proves the pre-load
        // targets the right relation and does not filter the grid.
        $list = $admin->getList();

        $this->assertCount(1, $list);
        $loaded = $list->first();
        $this->assertInstanceOf(Icon::class, $loaded);
        $this->assertSame($file->ID, $loaded->Icon()->ID);
    }
}
