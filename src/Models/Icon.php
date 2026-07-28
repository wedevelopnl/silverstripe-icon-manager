<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Models;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * @property int $IconID
 * @property File $Icon
 * @method File Icon()
 */
class Icon extends DataObject
{
    /** @config */
    private static string $singular_name = 'Icon';

    /** @config */
    private static string $plural_name = 'Icons';

    /**
     * @var array<string, string>
     * @config
     */
    private static array $db = [
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
     * @var array<string>
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
        if ($imageField !== null) {
            $imageField->setFolderName('Icons');
            $imageField->setAllowedFileCategories('wedevelop/icon');
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

    public function getPreview(): DBField
    {
        return DBField::create_field(DBHTMLText::class, $this->Icon->getTag());
    }
}
