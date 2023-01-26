<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Models;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Html;
use SilverStripe\AssetAdmin\Model\ThumbnailGenerator;

/**
 * @param int IconID
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

    /**
     * @var array<string, string>
     * @config
     */
    private static array $dependencies = [
        'ThumbnailGenerator' => '%$' . ThumbnailGenerator::class,
    ];

    public ThumbnailGenerator $thumbnailGenerator;

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
        $width =  UploadField::config()->get('thumbnail_width');
        $height = UploadField::config()->get('thumbnail_height');

        return DBField::create_field(DBHTMLText::class, Html::createTag('img', [
            'src' => $this->thumbnailGenerator->generateThumbnailLink($this->Icon->File, (int)$width, (int)$height),
            'style' => 'width: ' . $width . '; height: ' . $height . '; display: inline-block',
        ]), '');
    }

    /**
     * Exists to support migration from the old model.
     *
     * @todo remove this when the migration task gets removed.
     * @internal
     *
     * @param array{ID: int, ClassName: string, LastEdited: string, Created: string, Title: string, IconID: int} $data
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

    public function setThumbnailGenerator(ThumbnailGenerator $generator): self
    {
        $this->thumbnailGenerator = $generator;
        return $this;
    }
}
