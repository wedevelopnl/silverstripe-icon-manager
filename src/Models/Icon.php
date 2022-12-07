<?php

declare(strict_types=1);

namespace TheWebmen\IconManager\Models;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;

class Icon extends DataObject
{
    /** @config */
    private static string $table_name = 'TheWebmen_Icon';

    /** @config */
    private static string $singular_name = 'Icon';

    /** @config */
    private static string $plural_name = 'Icons';

    /**
     * @var array<string, string>
     * @config
     */
    private static string $db = [
        'Title' => 'Varchar(255)',
    ];

    /**
     * @var array<string, string>
     * @config
     */
    private static array $has_one = [
        'Icon' => File::class,
    ];

    /**
     * @var string[]
     * @config
     */
    private static array $owns = [
        'Icon',
    ];

    /**
     * @var array<string, string>
     * @config
     */
    private static array $summary_fields = [
        'Title' => 'Title',
        'getPreview' => 'Preview',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        /** @var UploadField $imageField */
        $imageField = $fields->dataFieldByName('Icon');
        $imageField->setFolderName('Icons');
        $imageField->setAllowedExtensions(['svg']);
        $imageField->setDescription('Only SVG files are allowed');

        $html = $this->forTemplate();
        if ($html) {
            $fields->addFieldToTab('Root.Main', HeaderField::create('PreviewHeader', 'Preview:', 3));
            $fields->addFieldToTab('Root.Main', LiteralField::create('Preview', "<div style='max-width: 20px;'>{$html}</div>"));
        }

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    public function searchableFields(): array
    {
        $fields = parent::searchableFields();
        unset($fields['getPreview']);
        return $fields;
    }

    public function forTemplate(): string
    {
        if (!$this->Icon()) {
            return '';
        }
        return $this->Icon()->getString();
    }

    public function getPreview(): DBField
    {
        return DBField::create_field(DBHTMLText::class, '<span style="width: 24px; display: inline-block;">' . $this->forTemplate() . '</span>');
    }

    public function onBeforeWrite(): void
    {
        if ($this->IconID) {
            /** @var File|null $file */
            $file = File::get_by_id($this->IconID);

            if (!$file) {
                throw new \Exception("File with ID: $this->IconID not found");
            }

            $filename = $file->getFilename();
            $fileParts = explode('.', $filename);
            $ext = $fileParts[count($fileParts) - 1];

            if ($ext !== 'svg') {
                throw new \Exception('File does not have the extension: svg');
            }
        }

        parent::onBeforeWrite();
    }
}
