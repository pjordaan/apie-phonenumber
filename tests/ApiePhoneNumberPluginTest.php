<?php


namespace W2w\Test\ApiePhoneNumberPlugin;

use erasys\OpenApi\Spec\v3\Schema;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\TestCase;
use W2w\Lib\Apie\DefaultApie;
use W2w\Lib\Apie\Plugins\Core\Serializers\SymfonySerializerAdapter;
use W2w\Lib\ApiePhoneNumberPlugin\ApiePhoneNumberPlugin;

class ApiePhoneNumberPluginTest extends TestCase
{
    public function testSchema_is_correct()
    {
        $apie = DefaultApie::createDefaultApie(true, [new ApiePhoneNumberPlugin('NL')]);
        $expected = new Schema([
            'type' => 'string',
            'format' => 'phone',
        ]);
        $actual = $apie->getSchemaGenerator()->createSchema(PhoneNumber::class, 'get', ['read', 'get']);
        $this->assertEquals($expected, $actual);
    }

    public function testDenormalizer_is_correct()
    {
        $apie = DefaultApie::createDefaultApie(true, [new ApiePhoneNumberPlugin('NL')]);
        $resourceSerializer = $apie->getResourceSerializer();
        if (!($resourceSerializer instanceof SymfonySerializerAdapter)) {
            $this->fail('Reource serializer should be a SymfonySerializerAdapter');
        }
        $serializer = $resourceSerializer->getSerializer();
        $actual = $serializer->denormalize('0611223344', PhoneNumber::class, null, []);
        $this->assertEquals('+31611223344', PhoneNumberUtil::getInstance()->format($actual, PhoneNumberFormat::E164));

        $actual = $serializer->denormalize('+32611223344', PhoneNumber::class, null, []);
        $this->assertEquals('+32611223344', PhoneNumberUtil::getInstance()->format($actual, PhoneNumberFormat::E164));
    }

    public function testNormalizer_is_correct()
    {
        $apie = DefaultApie::createDefaultApie(true, [new ApiePhoneNumberPlugin('NL')]);
        $resourceSerializer = $apie->getResourceSerializer();
        if (!($resourceSerializer instanceof SymfonySerializerAdapter)) {
            $this->fail('Reource serializer should be a SymfonySerializerAdapter');
        }
        $serializer = $resourceSerializer->getSerializer();
        $actual = $serializer->normalize(PhoneNumberUtil::getInstance()->parse('0611223344', 'NL'));
        $this->assertEquals('+31611223344', $actual);
    }
}
