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
        // The client script binds to this class. Applying it here rather than
        // leaving it to the caller is what makes the live preview work at all.
        $this->addExtraClass('icondropdown');

        Requirements::javascript('wedevelopnl/silverstripe-icon-manager:client/dist/icondropdownfield.js');
    }

    public function preview(): string
    {
        $iconID = $this->getRequest()->getVar('icon');

        if (!$iconID) {
            return _t(self::class . '.NO_ICON_SELECTED', 'No icon selected');
        }

        $icon = Icon::get()->byID($iconID);

        if (!$icon) {
            return _t(self::class . '.ICON_NOT_FOUND', 'Icon not created, please create it using the icon manager');
        }

        $iconFile = $icon->Icon();

        if (!$iconFile->exists()) {
            return _t(self::class . '.NO_PREVIEW_FILE', 'No icon preview file found, please attach a file to the icon');
        }

        // getTag(), not getString(): getString() returns raw file bytes, which
        // renders binary data into the DOM for any non-SVG icon file type.
        return $iconFile->getTag();
    }

    /** @return array<string, mixed> */
    #[Override]
    public function getAttributes(): array
    {
        /** @var array<string, mixed> $attributes */
        $attributes = parent::getAttributes();

        // Link() throws when the field is not attached to a form. The preview
        // endpoint is only meaningful once it is, so skip it while detached.
        // The vendor `FormField::getForm()` docblock claims a non-nullable
        // `Form`, but `$this->form` is genuinely nullable until attached; the
        // override below keeps this a real check instead of an always-true one.
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

        if ($this->getValue()) {
            $icon = Icon::get()->byID($this->getValue());
            if ($icon !== null && $icon->Icon()->exists()) {
                $iconPreview = $icon->Icon()->getTag();
            }
        }

        return $iconPreview;
    }
}
