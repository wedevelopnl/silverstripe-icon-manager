<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\Queries\SQLSelect;
use WeDevelop\IconManager\Models\Icon;

/**
 * @note when removing this task also remove `\WeDevelop\IconManager\Models\Icon::createFromOldDataset`
 * @phpstan-type OldIconShape array{ID: int, ClassName: string, LastEdited: string, Created: string, Title: string, IconID: int}
 */
class MigrateToNewIconModelTask extends BuildTask
{
    protected $title = 'Migrate icon data between 1.0.x and 2.0.x';

    /** @config */
    private static string $segment = 'migrate-icon-v1';

    protected $description = 'Migrate data from from the old Icon model into the new database tables';

    public function run($request)
    {
        /** @var array<OldIconShape> $oldIcons */
        $oldIcons = (new SQLSelect('*', 'TheWebmen_Icon'))->execute();
        foreach ($oldIcons as $oldIcon) {
            Icon::createFromOldDataset($oldIcon)->write();
        }
    }
}
