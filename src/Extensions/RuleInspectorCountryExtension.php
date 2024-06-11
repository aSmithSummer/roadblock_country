<?php

namespace aSmithSummer\RoadblockCountry\Extensions;

use aSmithSummer\Roadblock\Model\RequestLog;
use aSmithSummer\Roadblock\Model\Rule;
use aSmithSummer\Roadblock\Model\SessionLog;
use aSmithSummer\RoadblockCountry\Model\CountryIPRange;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;

class RuleInspectorCountryExtension extends DataExtension
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

    public function updatePrepareRequestLog(array &$requestLogData): void
    {
        $country = CountryIPRange::getCountryForIP($requestLogData['IPAddress']);
        if ($country) {
            $requestLogData['Country'] = $country;
        }
    }

}
