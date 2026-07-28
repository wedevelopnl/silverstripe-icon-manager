<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tests\Integration\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use WeDevelop\IconManager\Forms\IconDropdownField;

class IconDropdownFieldTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testExposesThePreviewEndpointWhenAttachedToAForm(): void
    {
        $field = IconDropdownField::create('IconID');
        Form::create(Controller::curr(), 'TestForm', FieldList::create($field), FieldList::create());

        $attributes = $field->getAttributes();

        $this->assertArrayHasKey('data-icon-preview-endpoint', $attributes);
        $this->assertStringContainsString('preview', (string) $attributes['data-icon-preview-endpoint']);
    }

    public function testOmitsThePreviewEndpointWhenDetachedFromAForm(): void
    {
        $this->assertArrayNotHasKey(
            'data-icon-preview-endpoint',
            IconDropdownField::create('IconID')->getAttributes(),
        );
    }
}
