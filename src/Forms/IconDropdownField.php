<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Forms;

use SilverStripe\Forms\DropdownField;
use WeDevelop\IconManager\Models\Icon;

class IconDropdownField extends DropdownField
{
    public function __construct($name, $title = 'Icon')
    {
        $source = Icon::get()->map();

        parent::__construct($name, $title, $source);
        $this->setHasEmptyDefault(true);
    }
}
