<?php

namespace W2w\Lib\ApiePhoneNumberPlugin\ValueObjects;

use libphonenumber\CountryCodeToRegionCodeMap;
use libphonenumber\PhoneNumberUtil;
use W2w\Lib\Apie\Interfaces\ValueObjectInterface;
use W2w\Lib\Apie\Plugins\ValueObject\ValueObjects\StringEnumTrait;

final class PhoneCountryCode implements ValueObjectInterface
{
    use StringEnumTrait;

    final public static function getValidValues()
    {
        $list = [];
        foreach (CountryCodeToRegionCodeMap::$countryCodeToRegionCodeMap as $number => $regions) {
            $list = array_merge($list, array_filter($regions, function ($region) { return $region !== PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY && $region !== PhoneNumberUtil::UNKNOWN_REGION; }));
        };
        return array_combine($list, $list);
    }
}
