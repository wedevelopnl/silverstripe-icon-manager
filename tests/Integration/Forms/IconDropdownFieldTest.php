<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tests\Integration\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use WeDevelop\IconManager\Forms\IconDropdownField;

class IconDropdownFieldTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testExposesThePreviewEndpointWhenAttachedToAForm(): void
    {
        $field = IconDropdownField::create('IconID');
        Form::create(new IconDropdownFieldTestController(), 'TestForm', FieldList::create($field), FieldList::create());

        $attributes = $field->getAttributes();

        $this->assertArrayHasKey('data-icon-preview-endpoint', $attributes);
        $this->assertStringContainsString('preview', (string) $attributes['data-icon-preview-endpoint']);
    }

    public function testExposesTheTranslatablePreviewMessagesToTheScript(): void
    {
        $field = IconDropdownField::create('IconID');
        Form::create(new IconDropdownFieldTestController(), 'TestForm', FieldList::create($field), FieldList::create());

        $attributes = $field->getAttributes();

        $this->assertSame('No icon selected', $attributes['data-icon-preview-empty'] ?? null);
        $this->assertSame('Loading preview…', $attributes['data-icon-preview-loading'] ?? null);
        $this->assertSame('Could not load the icon preview', $attributes['data-icon-preview-error'] ?? null);
    }

    public function testOmitsThePreviewEndpointWhenDetachedFromAForm(): void
    {
        $this->assertArrayNotHasKey(
            'data-icon-preview-endpoint',
            IconDropdownField::create('IconID')->getAttributes(),
        );
    }
}

// Form::FormAction() calls the controller's Link(), which triggers a PHP
// warning for any Controller without a configured url_segment (Controller::curr()
// has none in a test run). A minimal controller with one avoids the warning.
class IconDropdownFieldTestController extends Controller implements TestOnly
{
    private static string $url_segment = 'icon-dropdown-field-test';
}
