<?php


namespace W2w\Lib\ApiePhoneNumberPlugin;

use erasys\OpenApi\Spec\v3\Schema;
use libphonenumber\PhoneNumber;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use W2w\Lib\Apie\PluginInterfaces\ApieAwareInterface;
use W2w\Lib\Apie\PluginInterfaces\ApieAwareTrait;
use W2w\Lib\Apie\PluginInterfaces\NormalizerProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\SchemaProviderInterface;
use W2w\Lib\ApiePhoneNumberPlugin\Normalizers\PhoneAndCountryNormalizer;
use W2w\Lib\ApiePhoneNumberPlugin\Normalizers\PhoneNumberNormalizer;

class ApiePhoneNumberPlugin implements SchemaProviderInterface, NormalizerProviderInterface, ApieAwareInterface
{
    use ApieAwareTrait;

    private $defaultCountryCode;

    public function __construct(string $defaultCountryCode = 'US')
    {
        $this->defaultCountryCode = $defaultCountryCode;
    }
    /**
     * @return Schema[]
     */
    public function getDefinedStaticData(): array
    {
        return [
            PhoneNumber::class => new Schema([
                'type' => 'string',
                'format' => 'phone',
            ])
        ];
    }

    /**
     * @return callable[]
     */
    public function getDynamicSchemaLogic(): array
    {
       return [];
    }

    /**
     * @return NormalizerInterface[]|DenormalizerInterface[]
     */
    public function getNormalizers(): array
    {
        return [
            new PhoneNumberNormalizer($this->defaultCountryCode),
            new PhoneAndCountryNormalizer($this->getApie()->getPropertyConverter()),
        ];
    }
}
