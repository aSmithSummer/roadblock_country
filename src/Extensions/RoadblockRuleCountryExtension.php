<?php

namespace RoadblockCountry\Extensions;

use Roadblock\Model\RequestLog;
use Roadblock\Model\RoadblockRule;
use Roadblock\Model\SessionLog;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;

/**
 * Tracks a session.
 */
class RoadblockRuleCountryExtension extends DataExtension
{
    private static array $db = [
        'Country' => 'Varchar(250)',
        'CountryAllowed' => 'Boolean',
        'CountryNumber' => 'Int',
        'CountryOffset' => 'Int',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $order = [
            'Country' => 'ExcludePermission',
            'CountryAllowed' => 'Country',
            'CountryNumber' => 'CountryAllowed',
            'CountryOffset' => 'CountryNumber',
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
            'CountryAllowed' => 'CountryAllowed',
            'CountryNumber' => 'CountryNumber',
            'CountryOffset' => 'CountryOffset',
        ];

        foreach($extraFields as $k => $v) {
            $fields[$k] = $v;
        }

        return true;
    }

    public function updateEvaluateSession(SessionLog $sessionLog, RequestLog $request, RoadblockRule $rule, $global = false): bool
    {
        if ($rule->Country) {
            $time = DBDatetime::create()
                ->modify($sessionLog->LastAccessed)
                ->modify('-' . $rule->TypeStartOffset . ' seconds')
                ->format('y-MM-dd HH:mm:ss');

            $filter = [
                'Created:GreaterThanOrEqual' => $time,
            ];

            if ($global) {
                $filter['IPAddress'] = $request->IPAddress;
            } else {
                $filter['SessionLogID'] = $sessionLog->ID;
            }

            if ($rule->CountryAllowed === 'Allowed') {
                $filter['Country'] = $rule->Country;
            } else {
                $filter['Country:Not'] = $rule->Country;
            }

            $requests = RequestLog::get()->filter($filter);

            if ($requests->exists() && $requests->count() <= $rule->CountryNumber) {
                return true;
            }
        }
        return false;
    }

}
