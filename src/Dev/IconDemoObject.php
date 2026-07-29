<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Dev;

use Override;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use WeDevelop\IconManager\Forms\IconDropdownField;
use WeDevelop\IconManager\Models\Icon;

/**
 * Dev-only host for an {@see IconDropdownField}, used by the E2E suite and for
 * manual testing in the Docker testbed. Excluded from the distributed archive
 * via .gitattributes export-ignore (not from --prefer-source or VCS installs).
 * Reachable only through {@see IconDemoAdmin}, which registers no CMS route
 * outside dev.
 */
class IconDemoObject extends DataObject
{
    /** @config */
    private static string $table_name = 'WeDevelop_IconManager_IconDemoObject';

    /** @config */
    private static string $singular_name = 'Icon demo';

    /** @config */
    private static string $plural_name = 'Icon demos';

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
        'Icon' => Icon::class,
    ];

    /**
     * @var array<string, string>
     * @config
     */
    private static array $summary_fields = [
        'Title' => 'Title',
    ];

    #[Override]
    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('IconID');
        $fields->addFieldToTab('Root.Main', IconDropdownField::create('IconID', 'Icon'));

        return $fields;
    }
}
