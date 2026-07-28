<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tests\Functional\Forms;

use SilverStripe\Assets\File;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use WeDevelop\IconManager\Forms\IconDropdownField;
use WeDevelop\IconManager\Models\Icon;

class IconDropdownFieldTest extends SapphireTest
{
    protected $usesDatabase = true;

    /**
     * A field attached to a form, with an optional `icon` GET var stubbed on its
     * own request. RequestHandler::getRequest() falls back to the controller's
     * request, so setRequest() is what isolates each case.
     */
    private function attachedField(?string $iconVar = null): IconDropdownField
    {
        $field = IconDropdownField::create('IconID');
        Form::create(new IconDropdownFieldTestController(), 'TestForm', FieldList::create($field), FieldList::create());
        $field->setRequest(new HTTPRequest('GET', '/', $iconVar === null ? [] : ['icon' => $iconVar]));

        return $field;
    }

    private function iconWithFile(string $contents): Icon
    {
        $file = File::create();
        $file->setFromString($contents, 'Icons/test.svg');
        $file->write();

        $icon = Icon::create(['Title' => 'Star']);
        $icon->IconID = $file->ID;
        $icon->write();

        return $icon;
    }

    public function testPreviewReturnsAMessageWhenNoIconRequested(): void
    {
        $field = $this->attachedField();

        $this->assertSame('No icon selected', $field->preview());
    }

    public function testPreviewReturnsAMessageForAnUnknownIcon(): void
    {
        $this->assertStringContainsString('icon manager', $this->attachedField('99999')->preview());
    }

    public function testPreviewReturnsAMessageWhenTheIconHasNoFile(): void
    {
        $icon = Icon::create(['Title' => 'Empty']);
        $icon->write();

        $preview = $this->attachedField((string) $icon->ID)->preview();

        $this->assertStringContainsString('no icon preview file', strtolower($preview));
    }

    public function testPreviewRendersMarkupNotRawBytes(): void
    {
        $icon = $this->iconWithFile('<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $preview = $this->attachedField((string) $icon->ID)->preview();

        $this->assertNotSame('', $preview);
        $this->assertSame($icon->Icon()->getTag(), $preview);
    }

    public function testGetIconPreviewReturnsNullWhenNoValueIsSet(): void
    {
        $this->assertNull(IconDropdownField::create('IconID')->getIconPreview());
    }

    public function testGetIconPreviewReturnsNullForAnUnknownIcon(): void
    {
        $field = IconDropdownField::create('IconID');
        $field->setValue('99999');

        $this->assertNull($field->getIconPreview());
    }

    public function testGetIconPreviewRendersTheSelectedIcon(): void
    {
        $icon = $this->iconWithFile('<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $field = IconDropdownField::create('IconID');
        $field->setValue((string) $icon->ID);

        $this->assertSame($icon->Icon()->getTag(), $field->getIconPreview());
    }
}

// Form::FormAction() calls the controller's Link(), which triggers a PHP warning
// for any Controller without a configured url_segment (Controller::curr() has
// none in a test run). A minimal controller with one avoids the warning.
class IconDropdownFieldTestController extends Controller implements TestOnly
{
    private static string $url_segment = 'icon-dropdown-field-functional-test';
}
