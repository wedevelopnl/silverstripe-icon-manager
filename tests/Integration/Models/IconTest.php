<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tests\Integration\Models;

use SilverStripe\Assets\File;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\i18n\i18n;
use WeDevelop\IconManager\Models\Icon;

class IconTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testGetCMSFieldsConfiguresTheUploadField(): void
    {
        $icon = Icon::create();

        $fields = $icon->getCMSFields();

        $this->assertInstanceOf(FieldList::class, $fields);
        $uploadField = $fields->dataFieldByName('Icon');
        $this->assertNotNull($uploadField);
        $this->assertSame('Icons', $uploadField->getFolderName());
    }

    public function testSearchableFieldsExcludesThePreviewColumn(): void
    {
        $this->assertArrayNotHasKey('getPreview', Icon::create()->searchableFields());
    }

    public function testTranslatesTheModelNamesAndGridColumns(): void
    {
        i18n::with_locale('nl_NL', function (): void {
            $icon = Icon::create();

            $this->assertSame('Icoon', $icon->i18n_singular_name());
            $this->assertSame('Iconen', $icon->i18n_plural_name());
            $this->assertSame(['Title' => 'Titel', 'getPreview' => 'Voorbeeld'], $icon->summaryFields());
        });
    }

    public function testGetPreviewIsEmptyWhenNoFileIsAttached(): void
    {
        $icon = Icon::create(['Title' => 'No file']);
        $icon->write();

        $this->assertSame('', (string) $icon->getPreview());
    }

    public function testGetPreviewRendersTheAttachedFile(): void
    {
        $file = File::create();
        $file->setFromString('<svg xmlns="http://www.w3.org/2000/svg"></svg>', 'Icons/test.svg');
        $file->write();

        $icon = Icon::create(['Title' => 'With file']);
        $icon->IconID = $file->ID;
        $icon->write();

        $this->assertNotSame('', (string) $icon->getPreview());
    }
}
