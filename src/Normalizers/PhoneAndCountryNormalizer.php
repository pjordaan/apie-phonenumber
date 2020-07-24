<?php


namespace W2w\Lib\ApiePhoneNumberPlugin\Normalizers;

use libphonenumber\PhoneNumber;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use W2w\Lib\ApieObjectAccessNormalizer\Exceptions\CouldNotConvertException;
use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneAndCountry;
use W2w\Lib\ApiePhoneNumberPlugin\ValueObjects\PhoneCountryCode;

class PhoneAndCountryNormalizer implements DenormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var string
     */
    private $phoneNumberKey;

    /**
     * @var string
     */
    private $countryCodeKey;

    public function __construct(NameConverterInterface $nameConverter)
    {
        $this->phoneNumberKey = $nameConverter->normalize('phoneNumber');
        $this->countryCodeKey = $nameConverter->normalize('countryCode');
    }

    public function denormalize($data, $type, $format = null, $context = [])
    {
        if (!is_array($data) || !isset($data[$this->phoneNumberKey]) || !isset($data[$this->countryCodeKey])) {
            throw new CouldNotConvertException(
                json_encode([$this->phoneNumberKey => 'string', $this->countryCodeKey => 'string']),
                json_encode($data)
            );
        }
        /** @var PhoneCountryCode $country */
        $country = $this->serializer->denormalize(
            $data[$this->countryCodeKey],
            PhoneCountryCode::class,
            $format
        );
        /** @var PhoneNumber $phoneNumber */
        $phoneNumber = $this->serializer->denormalize(
            $data[$this->phoneNumberKey],
            PhoneNumber::class,
            $format,
            ['countryCode' => $country->toNative()]
        );

        return new PhoneAndCountry($phoneNumber, $country);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === PhoneAndCountry::class;
    }
}
