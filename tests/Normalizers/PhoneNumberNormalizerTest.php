<?php


namespace W2w\Test\ApiePhoneNumberPlugin\Normalizers;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use W2w\Lib\Apie\Exceptions\InvalidValueForValueObjectException;
use W2w\Lib\ApiePhoneNumberPlugin\Normalizers\PhoneNumberNormalizer;

class PhoneNumberNormalizerTest extends TestCase
{
    public function testDenormalize()
    {
        $serializer = new Serializer([new PhoneNumberNormalizer('US')], [new JsonEncoder()]);
        $actual = $serializer->denormalize('+31611223344', PhoneNumber::class);
        $expected = PhoneNumberUtil::getInstance()->parse('0611223344', 'NL');
        $this->assertEquals($expected, $actual);
    }

    public function testDenormalizeDefaultRegion()
    {
        $serializer = new Serializer([new PhoneNumberNormalizer('NL')], [new JsonEncoder()]);
        $actual = $serializer->denormalize('0611223344', PhoneNumber::class);
        $expected = PhoneNumberUtil::getInstance()->parse('0611223344', 'NL');
        $this->assertEquals($expected, $actual);
    }

    public function testDenormalizeError()
    {
        $serializer = new Serializer([new PhoneNumberNormalizer('NL')], [new JsonEncoder()]);
        $this->expectException(InvalidValueForValueObjectException::class);
        $serializer->denormalize(
            PhoneNumberUtil::getInstance()->getInvalidExampleNumber('NL'),
            PhoneNumber::class
        );
    }

    public function testNormalize()
    {
        $serializer = new Serializer([new PhoneNumberNormalizer('US')], [new JsonEncoder()]);
        $phoneNumber = PhoneNumberUtil::getInstance()->parse('0611223344', 'NL');
        $actual = $serializer->normalize($phoneNumber);
        $expected = '+31611223344';
        $this->assertEquals($expected, $actual);
    }
}
