<?php

namespace RoadblockCountry\Admin;

use RoadblockCountry\BulkLoader\CountryIPRangeCsvBulkLoader;
use RoadblockCountry\Model\CountryIPRange;
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

    private static array $allowed_actions = [
        'ImportForm',
    ];

    private static string $required_permission_codes = 'CMS_ACCESS_CountryIPRangeAdmin';

}
