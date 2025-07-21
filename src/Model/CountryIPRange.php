<?php

namespace aSmithSummer\RoadblockCountry\Model;

use aSmithSummer\Roadblock\Model\IPRule;
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
        'FromIPAddress' => 'Varchar(45)',
        'ToIPNumber' => 'BigInt',
        'ToIPAddress' => 'Varchar(45)',
        'CountryCode' => 'Varchar(8)',
        'CountryName' => 'Varchar(255)',
    ];

    private static array $summary_fields = [
        'FromIPAddress' => 'From',
        'ToIPAddress' => 'To',
        'CountryName' => 'Country',
    ];

    private static string $default_sort = 'FromIPNumber';

    public function validate()
    {
        $result = parent::validate();

        if($this->FromIPAddress) {
            if (!((int) IPRule::ipToNumeric($this->FromIPAddress) >= 0)) {
                $result->addError(_t(__CLASS__ . '.FROM_VALIDATIOM',"From ip address not found."));
            }
        } else {
            $result->addError(_t(__CLASS__ . '.FROM_VALIDATIOM',"From ip address not found."));
        }

        if($this->ToIPAddress) {
            if (!((int) IPRule::ipToNumeric($this->ToIPAddress) >= 0)) {
                $result->addError(_t(__CLASS__ . '.TO_VALIDATIOM',"To ip address not found."));
            }
        } else {
            $result->addError(_t(__CLASS__ . '.TO_VALIDATIOM',"To ip address not found."));
        }

        if(
            $this->ToIPAddress
            && $this->FromIPAddress
            && (int) IPRule::ipToNumeric($this->ToIPAddress) < (int) IPRule::ipToNumeric($this->FromIPAddress)
        ) {
            $result->addError(_t(
                __CLASS__ . '.ORDER_VALIDATIOM',
                "From ip address must be greater than ip address."
            ));
        }

        return $result;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->FromIPNumber) {
            $this->FromIPNumber = (int) IPRule::ipToNumeric((string) $this->FromIPAddress);
        }

        if (!$this->ToIPNumber) {
            $this->ToIPNumber = (int) IPRule::ipToNumeric($this->ToIPAddress);
        }
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        //check if existing range exists and amend for before / after / inside or both.
        $records = self::get()
            ->exclude(['ID' => $this->ID])
            ->where(
                sprintf('("CountryIPRange"."FromIPNumber" BETWEEN %1$d AND %2$d) OR ("CountryIPRange"."ToIPNumber" BETWEEN %1$d AND %2$d) ' .
                'OR ("CountryIPRange"."FromIPNumber" < %1$d AND "CountryIPRange"."ToIPNumber" > %2$d)',
                $this->FromIPNumber,
                $this->ToIPNumber)
            );

        foreach ($records as $record) {
            if ($record->FromIPNumber >= $this->FromIPNumber && $record->FromIPNumber <= $this->ToIPNumber) {
                if ($record->ToIPNumber <= $this->ToIPNumber) {
                    $record->delete();

                    continue;
                }

                $record->FromIPNumber = $this->ToIPNumber + 1;
                $record->FromIPAddress = IPRule::numericToIp((string) $record->FromIPNumber);
                $record->write();

                continue;
            }

            if ($record->ToIPNumber <= $this->FromIPNumber) {
                $record->ToIPNumber = $this->FromIPNumber - 1;
                $record->ToIPAddress= IPRule::numericToIp((string) $record->ToIPNumber);
                $record->write();

                continue;
            }

            $record2 = $record->duplicate(false);
            $record->ToIPNumber = $this->FromIPNumber - 1;
            $record->ToIPAddress= numericToIp((string) $record->ToIPNumber);
            $record2->FromIPNumber = $this->ToIPNumber + 1;
            $record2->FromIPAddress = IPRule::numericToIp((string) $record2->FromIPNumber);
            $record->write();
            $record2->write();
        }
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canCreate($member = null, $context = []): bool
    {
        return Permission::check('CountryIPRangeAdmin', 'any', $member);
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canView($member = null): bool
    {
        return Permission::check('CountryIPRangeAdmin', 'any', $member);
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canEdit($member = null): bool
    {
        return Permission::check('CountryIPRangeAdmin', 'any', $member);
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function canDelete($member = null): bool
    {
        return Permission::check('CountryIPRangeAdmin', 'any', $member);
    }

    public function getExportFields(): array
    {
        $fields =  [
            'Title' => 'Title',
            'FromIPNumber' => 'FromIPNumber',
            'FromIPAddress' => 'FromIPAddress',
            'ToIPNumber' => 'ToIPNumber',
            'ToIPAddress' => 'ToIPAddress',
            'CountryCode' => 'CountryCode',
            'CountryName' => 'CountryName',
        ];

        $this->extend('updateExportFields', $fields);

        return $fields;
    }

    public static function getCountryForIP(?string $ip): string
    {
        $default = self::config()->get('default_country_name');

        if (!$ip) {
            return $default;
        }

        $record = self::get()->where(sprintf('%s BETWEEN "CountryIPRange"."FromIPNumber" AND "CountryIPRange"."ToIPNumber"', IPRule::ipToNumeric($ip)))->first();

        return $record ? $record->CountryName : $default;
    }

}
