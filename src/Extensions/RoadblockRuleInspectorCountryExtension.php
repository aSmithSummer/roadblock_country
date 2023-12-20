<?php

namespace aSmithSummer\RoadblockCountry\Extensions;

use aSmithSummer\Roadblock\Model\RequestLog;
use aSmithSummer\Roadblock\Model\RoadblockRule;
use aSmithSummer\Roadblock\Model\SessionLog;
use aSmithSummer\RoadblockCountry\Model\CountryIPRange;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;

class RoadblockRuleInspectorCountryExtension extends DataExtension
{
    private static array $db = [
        'Country' => 'Varchar(250)',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $order = [
            'Country' => 'LoginAttemptStatus',
        ];

        foreach ($order as $fieldName => $after) {
            $field = $fields->dataFieldByName($fieldName);
            $fields->insertAfter($after, $field);
        }
    }

    public function updateExportFields(&$fields): bool
    {
        $extraFields = [
            'Country' => 'Country',
        ];

        foreach ($extraFields as $k => $v) {
            $fields[$k] = $v;
        }

        return true;
    }

    public function updateSetRequestLogData(array &$requestLogData): void
    {
        $country = CountryIPRange::getCountryForIP($requestLogData['IPAddress']);
        if ($country) {
            $requestLogData['Country'] = $country;
        }
    }

}
