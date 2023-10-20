<?php

namespace RoadblockCountry\Extensions;

use Roadblock\Model\RequestLog;
use Roadblock\Model\RoadblockRule;
use Roadblock\Model\SessionLog;
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

    public function updateSetRequestLogData(array &$requestLogData): void
    {
        $country = $this->owner->Country;
        if ($country) {
            $requestLogData['Country'] = $country;
        }
    }

}