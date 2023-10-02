<?php

namespace RoadblockCountry\BulkLoader;

use SilverStripe\Dev\CsvBulkLoader;

/**
 * Updates Country IP Range records from a CSV file
 */
class CountryIPRangeCsvBulkLoader extends CsvBulkLoader
{

    /**
     * @var array
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    public $columnMap = [
        'From IP Number' => 'FromIPNumber',
        'From IP Address' => 'FromIPAddress',
        'To IP Number' => 'ToIPNumber',
        'To IP Address' => 'ToIPAddress',
        'Country' => 'CountryCode',
        'Country Name' => 'CountryName',
    ];

}
