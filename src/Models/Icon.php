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
 *
 * @phpstan-import-type OldIconShape from \WeDevelop\IconManager\Tasks\MigrateToNewIconModelTask
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

    /**
     * @deprecated 2.0.1 Call the `getTag` method straight on the Icon object
     */
    public function forTemplate(): ?string
    {
        return $this->Icon->getTag();
    }

    public function getPreview(): DBField
    {
        return DBField::create_field(DBHTMLText::class, $this->Icon->getTag());
    }

    /**
     * Exists to support migration from the old model.
     *
     * @todo remove this when the migration task gets removed.
     * @internal
     *
     * @param OldIconShape $data
     */
    public static function createFromOldDataset(array $data): self
    {
        return self::create([
            'ID' => $data['ID'],
            'ClassName' => self::class,
            'LastEdited' => $data['LastEdited'],
            'Created' => $data['Created'],
            'Title' => $data['Title'],
            'IconID' => $data['IconID'],
        ]);
    }
}
