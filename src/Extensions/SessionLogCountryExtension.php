<?php

namespace aSmithSummer\RoadblockCountry\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Tracks a session.
 */
class SessionLogCountryExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields): void
    {
        $fields->insertAfter('LastAccessed', TextField::create(
            'LatestCountry',
            'Country',
            $this->owner->Requests()->first()->Country
        ));
    }

}
