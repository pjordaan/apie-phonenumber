<?php

namespace W2w\Lib\ApiePhoneNumberPlugin\Normalizers;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use W2w\Lib\Apie\Exceptions\InvalidValueForValueObjectException;
use W2w\Lib\ApiePhoneNumberPlugin\Interfaces\HasCountryCodeInterface;

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
            return $this->validate($data, PhoneNumberUtil::getInstance()->parse($data));
        } catch (NumberParseException $parseException) {
        }
        try {
            return $this->validate(
                $data,
                PhoneNumberUtil::getInstance()->parse(
                    $data,
                    $this->determineCountryCode($context)
                )
            );
        } catch (NumberParseException $parseException) {
            throw new InvalidValueForValueObjectException($data, PhoneNumber::class);
        }
    }

    private function validate($data, PhoneNumber $phoneNumber): PhoneNumber
    {
        if ($phoneNumber->hasCountryCode() && $phoneNumber->getCountryCode() !== PhoneNumberUtil::UNKNOWN_REGION) {
            return $phoneNumber;
        }
        throw new InvalidValueForValueObjectException($data, PhoneNumber::class);
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

    private function determineCountryCode(array $context): string
    {
        if (isset($context['countryCode']) && is_string($context['countryCode'])) {
            return $context['countryCode'];
        }
        if (!empty($context['object_hierarchy']) && $context['object_hierarchy'] && is_array($context['object_hierarchy'])) {
            $lastObject = $context['object_hierarchy'][count($context['object_hierarchy']) - 1];
            if ($lastObject instanceof HasCountryCodeInterface) {
                return $lastObject->getCountryCode()->toNative();
            }
        }
        return $this->defaultCountryCode;
    }
}
