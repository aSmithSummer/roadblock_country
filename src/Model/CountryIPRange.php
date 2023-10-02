<?php

namespace RoadblockCountry\Model;

use App\Helpers\GroupNames;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

/**
 * Class CountryIPRange
 *
 * @property int $FromIPNumber
 * @property string $FromIPAddress
 * @property int $ToIPNumber
 * @property string $ToIPAddress
 * @property string $CountryCode
 * @property string $CountryName
 */
class CountryIPRange extends DataObject
{

    private static string $table_name = 'CountryIPRange';

    private static array $db = [
        'FromIPNumber' => 'BigInt',
        'FromIPAddress' => 'Varchar(15)',
        'ToIPNumber' => 'BigInt',
        'ToIPAddress' => 'Varchar(15)',
        'CountryCode' => 'Varchar(8)',
        'CountryName' => 'Varchar(255)',
    ];

    private static array $summary_fields = [
        'FromIPAddress' => 'From',
        'ToIPAddress' => 'To',
        'CountryName' => 'Country',
    ];

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canCreate($member = null, $context = []): bool
    {
        return Permission::check('ADMIN', 'any');
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canView($member = null): bool
    {
        return Permission::check('ADMIN', 'any');
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canEdit($member = null): bool
    {
        return Permission::check('ADMIN', 'any');
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canDelete($member = null): bool
    {
        return false;
    }

    public static function getCountryForIP(?string $ip): string
    {
        $outside = 'Outside New Zealand';

        if (!$ip) {
            return $outside;
        }

        $record = self::get()->where(sprintf('%s BETWEEN FromIPNumber AND ToIPNumber', ip2long($ip)))->first();

        return $record ? $record->CountryName : $outside;
    }

}
