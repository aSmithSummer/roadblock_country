<?php

namespace aSmithSummer\RoadblockCountry\Extensions;

use aSmithSummer\Roadblock\Model\RequestLog;
use aSmithSummer\Roadblock\Model\RoadblockRule;
use aSmithSummer\Roadblock\Model\SessionLog;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;

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

    public function getDescriptionNice(&$text): void
    {
        if ($this->owner->Country) {
            $allowed = 'is';

            if ($this->owner->CountryAllowed) {
                $allowed = 'is not';
            }

            $text .= _t(
                self::class . '.DESCRIPTION',
                '<p>The country {Country} {Allowed} in {Number} requests in the last {Offset} seconds</p>'
                ,[
                    'Country' => $this->owner->Country,
                    'Allowed' => $allowed,
                    'Number' => $this->owner->CountryNumber,
                    'Offset' => $this->owner->CountryOffset,
                ]
            );
        }
    }

    public function updateEvaluateSession(SessionLog $sessionLog, RequestLog $request, RoadblockRule $rule, $global = false): bool
    {
        if ($rule->Country) {
            $time = DBDatetime::create()
                ->modify($sessionLog->LastAccessed)
                ->modify('-' . $rule->StartOffset . ' seconds')
                ->format('y-MM-dd HH:mm:ss');

            if ($global) {
                $filter['IPAddress'] = $request->IPAddress;
            } else {
                $filter['SessionLogID'] = $sessionLog->ID;
            }

            if ($rule->CountryAllowed) {
                $filter['Country'] = $rule->Country;
                $requests = $this->owner->getRequestLogs($filter, $time);
            } else {
                //so we are compatible with both DataList and ArrayList filtering
                $exclude = ['Country' => $rule->Country];
                $requests = $this->owner->getRequestLogs($filter, $time)->exclude($exclude);
            }

            if ($requests->exists() && $requests->count() <= $rule->CountryNumber) {
                $rule->addExceptionData(_t(self::class . '.TEST_COUNTRY',
                    'Country exists and count {count} less than or equal to {number} for permission {allowed}',
                    [
                        'allowed' => $rule->CountryAllowed ? 'Allowed' : 'Denied',
                        'count' => $requests->count(),
                        'number' => $rule->CountryNumber,
                    ]));

                return true;
            }

            $rule->addExceptionData(_t(self::class . '.TEST_COUNTRY_FALSE',
                'Country does not exist or count {count} is greater than number {number} for permission {allowed}',
                [
                    'allowed' => $rule->CountryAllowed ? 'Allowed' : 'Denied',
                    'count' => $requests ? $requests->count() : 0,
                    'number' => $rule->CountryNumber,
                ]));
        }

        return false;
    }

}
