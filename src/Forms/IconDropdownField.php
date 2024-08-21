<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Forms;

use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use WeDevelop\IconManager\Models\Icon;

class IconDropdownField extends DropdownField
{
    private static array $allowed_actions = [
        'preview',
    ];

    public function __construct($name, $title = 'Icon')
    {
        parent::__construct($name, $title, Icon::get()->Sort('Title')->map());

        $this->setHasEmptyDefault(true);
    }

    public function preview(): string
    {
        $iconID = $this->getRequest()->getVar('icon');

        if (!$iconID) {
            return 'No icon selected';
        }

        $icon = Icon::get_by_id($iconID);

        if (!$icon) {
            return 'Icon not created, please create it using the icon manager';
        }

        $iconFile = $icon->Icon();

        if (!$iconFile->exists()) {
            return 'No icon preview file found, please attach a file to the icon';
        }

        return $iconFile->getString();
    }

    public function Field($properties = []): DBHTMLText
    {
        Requirements::javascript('wedevelopnl/silverstripe-icon-manager:client/js/icondropdownfield.js');

        $this->setAttribute('data-icon-preview-endpoint', $this->Link('preview'));

        return parent::Field($properties);
    }

    public function getIconPreview(): ?string
    {
        $iconPreview = null;

        if ($this->value) {
            $icon = Icon::get_by_id($this->value);
            if ($icon->Icon()->exists()) {
                $iconPreview = $icon->Icon()->getString();
            }
        }

        return $iconPreview;
    }
}
