<?php

namespace aSmithSummer\RoadblockCountry\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

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

        if (!$country) {
            return;
        }

        $requestLogData['Country'] = $country;
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
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

}
