<?php

namespace aSmithSummer\RoadblockCountry\Admin;

use aSmithSummer\RoadblockCountry\BulkLoader\CountryIPRangeCsvBulkLoader;
use aSmithSummer\RoadblockCountry\Model\CountryIPRange;
use SilverStripe\Admin\ModelAdmin;

/**
 * Class CountryIPRangeAdmin
 *
 */
class CountryIPRangeAdmin extends ModelAdmin
{

    protected bool $allow_import_overwrite = true;

    private static array $managed_models = [
        CountryIPRange::class,
    ];

    private static string $url_segment = 'countryipranges';

    private static string $menu_title = 'Country IP Ranges';

    private static $menu_icon_class = 'font-icon-globe';

    private static array $allowed_actions = [
        'ImportForm',
    ];

    private static string $required_permission_codes = 'CMS_ACCESS_CountryIPRangeAdmin';

}
