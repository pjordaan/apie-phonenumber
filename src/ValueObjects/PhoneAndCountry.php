<?php


namespace W2w\Lib\ApiePhoneNumberPlugin\ValueObjects;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use LogicException;
use W2w\Lib\ApiePhoneNumberPlugin\Interfaces\HasCountryCodeInterface;

final class PhoneAndCountry implements HasCountryCodeInterface
{
    /**
     * @var PhoneNumber
     */
    private $phoneNumber;

    /**
     * @var PhoneCountryCode
     */
    private $countryCode;

    public function __construct(PhoneNumber $phoneNumber, PhoneCountryCode $countryCode)
    {
        $expected = PhoneNumberUtil::getInstance()->getRegionCodeForCountryCode($phoneNumber->getCountryCode());
        if ($countryCode->toNative() !== $expected) {
            throw new LogicException('Country code ' . $countryCode->toNative() . ' does not match ' . $expected);
        }
        $this->phoneNumber = $phoneNumber;
        $this->countryCode = $countryCode;
    }

    /**
     * Get the phone number.
     *
     * @return PhoneNumber
     */
    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * Get the country code.
     *
     * @return PhoneCountryCode
     */
    public function getCountryCode(): PhoneCountryCode
    {
        return $this->countryCode;
    }
}
