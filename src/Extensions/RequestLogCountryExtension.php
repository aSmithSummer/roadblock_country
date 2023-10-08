<?php

namespace RoadblockCountry\Extensions;

use RoadblockCountry\Model\CountryIPRange;
use SilverStripe\ORM\DataExtension;

/**
 * Tracks a session.
 */
class RequestLogCountryExtension extends DataExtension
{
    private static array $db = [
        'Country' => 'Varchar(50)',
    ];

    public function updateCaptureRequestData(array $requestData): void
    {
        $this->owner->update(['Country' => CountryIPRange::getCountryForIP($requestData['IPAddress'])]);
    }

}
