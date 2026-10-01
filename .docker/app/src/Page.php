<?php

declare(strict_types=1);

use SilverStripe\CMS\Model\SiteTree;
use WeDevelop\IconManager\Forms\IconDropdownField;
use WeDevelop\IconManager\Models\Icon;

/**
 * Testbed page wired up as the "Using icons with a DataModel/Page" example in
 * docs/configuration.md, so E2E covers the documented integration.
 */
class Page extends SiteTree
{
    private static array $has_one = [
        'Icon' => Icon::class,
    ];

    private static array $owns = [
        'Icon',
    ];

    // Untyped like the docs example: ErrorPage extends Page with an untyped
    // getCMSFields(), so a return type here is a fatal signature mismatch.
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            IconDropdownField::create('IconID', 'Icon'),
        ]);

        return $fields;
    }
}
