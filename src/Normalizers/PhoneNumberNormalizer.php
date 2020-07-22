<?php

namespace W2w\Lib\ApiePhoneNumberPlugin\Normalizers;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use W2w\Lib\Apie\Exceptions\InvalidValueForValueObjectException;

class PhoneNumberNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private $defaultCountryCode;

    public function __construct(string $defaultCountryCode)
    {
        $this->defaultCountryCode = $defaultCountryCode;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        try {
            return PhoneNumberUtil::getInstance()->parse($data);
        } catch (NumberParseException $parseException) {
        }
        try {
            return PhoneNumberUtil::getInstance()->parse($data, $context['countryCode'] ?? $this->defaultCountryCode);
        } catch (NumberParseException $parseException) {
            throw new InvalidValueForValueObjectException($data, PhoneNumber::class);
        }
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === PhoneNumber::class;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var PhoneNumber $object */
        return PhoneNumberUtil::getInstance()->format($object, PhoneNumberFormat::E164);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PhoneNumber;
    }
}
