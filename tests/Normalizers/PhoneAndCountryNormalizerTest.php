<?php


namespace W2w\Test\ApiePhoneNumberPlugin\Normalizers;

use libphonenumber\PhoneNumberUtil;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Serializer;
use W2w\Lib\Apie\Plugins\ValueObject\Normalizers\ValueObjectNormalizer;
use W2w\Lib\ApieObjectAccessNormalizer\Exceptions\CouldNotConvertException;
use W2w\Lib\ApiePhoneNumberPlugin\Normalizers\PhoneAndCountryNormalizer;
use W2w\Lib\ApiePhoneNumberPlugin\Normalizers\PhoneNumberNormalizer;
use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneAndCountry;
use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneCountryCode;

class PhoneAndCountryNormalizerTest extends TestCase
{
    public function testDenormalizeFailureWrongKeys()
    {
        $testItem = new Serializer(
            [
                new PhoneNumberNormalizer('US'),
                new PhoneAndCountryNormalizer(new CamelCaseToSnakeCaseNameConverter()),
                new ValueObjectNormalizer(),
            ]
        );
        $input = [
            'phoneNumber' => '+12015550123',
            'countryCode' => 'NL'
        ];
        $this->expectException(CouldNotConvertException::class);
        $testItem->denormalize($input, PhoneAndCountry::class);
    }

    public function testDenormalizeFailure()
    {
        $testItem = new Serializer(
            [
                new PhoneNumberNormalizer('US'),
                new PhoneAndCountryNormalizer(new CamelCaseToSnakeCaseNameConverter()),
                new ValueObjectNormalizer(),
            ]
        );
        $input = [
            'phone_number' => '+12015550123',
            'country_code' => 'NL'
        ];
        $this->expectException(LogicException::class);
        $testItem->denormalize($input, PhoneAndCountry::class);
    }

    public function testDenormalize()
    {
        $testItem = new Serializer(
            [
                new PhoneNumberNormalizer('US'),
                new PhoneAndCountryNormalizer(new CamelCaseToSnakeCaseNameConverter()),
                new ValueObjectNormalizer(),
            ]
        );
        $input = [
            'phone_number' => '+12015550123',
            'country_code' => 'US'
        ];
        $expected = new PhoneAndCountry(
            PhoneNumberUtil::getInstance()->parse('+12015550123'),
            new PhoneCountryCode('US')
        );
        $actual = $testItem->denormalize($input, PhoneAndCountry::class);
        $this->assertEquals($expected, $actual);
    }
}
