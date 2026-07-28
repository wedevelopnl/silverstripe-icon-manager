<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Forms;

use Override;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\Form;
use SilverStripe\View\Requirements;
use WeDevelop\IconManager\Models\Icon;

class IconDropdownField extends DropdownField
{
    /**
     * @var array<string>
     * @config
     */
    private static array $allowed_actions = [
        'preview',
    ];

    public function __construct(string $name, string $title = 'Icon')
    {
        parent::__construct($name, $title, Icon::get()->sort(['Title' => 'ASC'])->map()->toArray());

        $this->setHasEmptyDefault(true);

        Requirements::javascript('wedevelopnl/silverstripe-icon-manager:client/dist/icondropdownfield.js');
    }

    public function preview(): string
    {
        $iconID = $this->getRequest()->getVar('icon');

        if (!$iconID) {
            return 'No icon selected';
        }

        $icon = Icon::get()->byID($iconID);

        if (!$icon) {
            return 'Icon not created, please create it using the icon manager';
        }

        $iconFile = $icon->Icon();

        if (!$iconFile->exists()) {
            return 'No icon preview file found, please attach a file to the icon';
        }

        return $iconFile->getString();
    }

    /** @return array<string, mixed> */
    #[Override]
    public function getAttributes(): array
    {
        /** @var array<string, mixed> $attributes */
        $attributes = parent::getAttributes();

        // Link() throws when the field is not attached to a form. The preview
        // endpoint is only meaningful once it is, so skip it while detached.
        /** @var Form|null $form */
        $form = $this->getForm();
        if ($form !== null) {
            $attributes['data-icon-preview-endpoint'] = $this->Link('preview');
        }

        return $attributes;
    }

    public function getIconPreview(): ?string
    {
        $iconPreview = null;

        if ($this->value) {
            /**
             * @deprecated FormField::Value() has been deprecated. It will be replaced by getFormattedValue() and getValue().
             * See: https://docs.silverstripe.org/en/5/changelogs/5.4.0/#deprecated-api
             */
            $icon = Icon::get()->byID($this->value);
            if ($icon !== null && $icon->Icon()->exists()) {
                $iconPreview = $icon->Icon()->getString();
            }
        }

        return $iconPreview;
    }
}
