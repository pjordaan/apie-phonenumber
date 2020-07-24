<?php


namespace W2w\Lib\ApiePhoneNumberPlugin\Interfaces;

use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneCountryCode;

interface HasCountryCodeInterface
{
    /**
     * Get the country code.
     *
     * @return PhoneCountryCode
     */
    public function getCountryCode(): PhoneCountryCode;
}
