<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Models;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Html;

/**
 * @param int IconID
 * @method File Icon()
 */
class Icon extends DataObject
{
    /** @config */
    private static string $singular_name = 'Icon';

    /** @config */
    private static string$plural_name = 'Icons';

    /** @config */
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
            $imageField->setAllowedExtensions(['svg']);
            $imageField->setDescription('Only SVG files are allowed');
        }

        $iconSvg = $this->forTemplate();
        if (!empty($iconSvg)) {
            $fields->addFieldToTab('Root.Main', HeaderField::create('PreviewHeader', 'Preview:', 3));
            $fields->addFieldToTab('Root.Main', LiteralField::create('Preview', Html::createTag('div', [
                'style' => 'max-width: 20px',
            ], $iconSvg)));
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
        return $this->Icon() ? $this->Icon()->getString() : '';
    }

    public function getPreview(): DBField
    {
        return DBField::create_field(DBHTMLText::class, Html::createTag('span', [
            'style' => 'width: 24px; display: inline-block',
        ], $this->forTemplate()));
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
}
