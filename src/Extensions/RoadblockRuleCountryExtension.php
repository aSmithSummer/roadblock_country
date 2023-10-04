<?php

namespace RoadblockMember\Extensions;

use Roadblock\Model\RequestLog;
use Roadblock\Model\RoadblockRule;
use Roadblock\Model\SessionLog;
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

    public function updateEvaluateSession(SessionLog $sessionLog, RequestLog $request, RoadblockRule $rule): bool
    {
        if ($rule->Country) {
            $time = DBDatetime::create()
                ->modify($sessionLog->LastAccessed)
                ->modify('-' . $rule->TypeStartOffset . ' seconds')
                ->format('y-MM-dd HH:mm:ss');

            $filter = [
                'SessionLogID' => $sessionLog->ID,
                'Created:GreaterThanOrEqual' => $time,

            ];

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
