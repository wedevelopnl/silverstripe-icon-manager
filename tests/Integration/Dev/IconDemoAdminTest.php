<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tests\Integration\Dev;

use ReflectionProperty;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use WeDevelop\IconManager\Dev\IconDemoAdmin;
use WeDevelop\IconManager\Dev\IconDemoObject;
use WeDevelop\IconManager\Forms\IconDropdownField;

class IconDemoAdminTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testIsHiddenFromTheCmsMenuByDefault(): void
    {
        // Reflection, not Config::inst(): SilverStripe's TestKernel forces
        // Kernel::getEnvironment() to 'dev' for every test run (see
        // vendor/silverstripe/framework/src/Dev/TestKernel.php), and the
        // "environment" YAML rule in vendor/silverstripe/config keys off that
        // same call — so _config/dev.yml's `Only: environment: dev` block is
        // unconditionally active here too, and Config::inst() would always
        // read back the dev.yml override, never the class's own default.
        // Reflection reads the declared default directly.
        $property = new ReflectionProperty(IconDemoAdmin::class, 'ignore_menuitem');

        $this->assertTrue(
            $property->getDefaultValue(),
            'The dev admin must stay out of the CMS menu unless dev config enables it.',
        );
    }

    public function testManagesTheDemoObject(): void
    {
        $this->assertSame([IconDemoObject::class], Config::inst()->get(IconDemoAdmin::class, 'managed_models'));
    }

    public function testDemoObjectExposesAnIconDropdownField(): void
    {
        $field = IconDemoObject::create()->getCMSFields()->dataFieldByName('IconID');

        $this->assertInstanceOf(IconDropdownField::class, $field);
    }
}
