<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Models;

use Override;
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
    // Matches the name SilverStripe already derives implicitly from the FQCN
    // (WeDevelop\IconManager\Models\Icon). Making it explicit satisfies
    // silverstan without renaming the table underneath existing installs.
    /** @config */
    private static string $table_name = 'WeDevelop_IconManager_Models_Icon';

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

    #[Override]
    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        /** @var UploadField|null $imageField */
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
    #[Override]
    public function searchableFields(): array
    {
        /** @var array<string, mixed> $fields */
        $fields = parent::searchableFields();
        unset($fields['getPreview']);
        return $fields;
    }

    public function getPreview(): DBField
    {
        $tag = $this->Icon()->exists() ? $this->Icon()->getTag() : '';

        return DBField::create_field(DBHTMLText::class, $tag);
    }
}
