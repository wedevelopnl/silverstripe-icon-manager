<?php

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
    /**
     * @var string
     */
    private static $table_name = 'TheWebmen_Icon';

    /**
     * @var string
     */
    private static $singular_name = 'Icon';

    /**
     * @var string
     */
    private static $plural_name = 'Icons';

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Icon' => File::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'Icon',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title',
        'getPreview' => 'Preview',
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
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
     * @return array
     */
    public function searchableFields()
    {
        $fields = parent::searchableFields();
        unset($fields['getPreview']);
        return $fields;
    }

    /**
     * @return string
     */
    public function forTemplate()
    {
        if (!$this->Icon()) {
            return '';
        }
        return $this->Icon()->getString();
    }

    /**
     * @return DBField
     */
    public function getPreview()
    {
        return DBField::create_field(DBHTMLText::class, '<span style="width: 24px; display: inline-block;">' . $this->forTemplate() . '</span>');
    }

    /**
     * @throws \Exception
     */
    public function onBeforeWrite()
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
