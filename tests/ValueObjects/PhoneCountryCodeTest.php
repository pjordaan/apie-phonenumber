<?php

namespace W2w\Test\ApiePhoneNumberPlugin\ValueObjects;

use PHPUnit\Framework\TestCase;
use W2w\Lib\Apie\Exceptions\InvalidValueForValueObjectException;
use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneCountryCode;

class PhoneCountryCodeTest extends TestCase
{
    public function testGetValidValues()
    {
        $values = PhoneCountryCode::getValidValues();
        $this->assertEquals($values, array_unique($values));
    }

    public function testConstructor()
    {
        $country = new PhoneCountryCode('US');
        $this->assertEquals('US', $country->toNative());
    }

    public function testInvalidValue()
    {
        $this->expectException(InvalidValueForValueObjectException::class);
        new PhoneCountryCode('us');
    }
}
