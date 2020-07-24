<?php

namespace W2w\Test\ApiePhoneNumberPlugin\ValueObjects;

use libphonenumber\PhoneNumberUtil;
use LogicException;
use PHPUnit\Framework\TestCase;
use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneAndCountry;
use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneCountryCode;

class PhoneAndCountryTest extends TestCase
{
    public function testConstructor()
    {
        $phoneNumber = PhoneNumberUtil::getInstance()->parse('+31611223344', 'NL');
        $countryCode = new PhoneCountryCode('NL');
        $testItem = new PhoneAndCountry($phoneNumber, $countryCode);
        $this->assertSame($phoneNumber, $testItem->getPhoneNumber());
        $this->assertSame($countryCode, $testItem->getCountryCode());
    }

    public function testCountryMismatchThrowsError()
    {
        $phoneNumber = PhoneNumberUtil::getInstance()->parse('+31611223344', 'NL');
        $countryCode = new PhoneCountryCode('US');
        $this->expectException(LogicException::class);
        new PhoneAndCountry($phoneNumber, $countryCode);
    }
}
