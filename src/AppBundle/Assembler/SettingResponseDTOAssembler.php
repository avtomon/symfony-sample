<?php

namespace AppBundle\Assembler;

use AppBundle\DTO\SettingResponseDTO;
use AppBundle\Entity\Setting;
use AppBundle\Manager\ConfigurationManager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SettingResponseDTOAssembler
 * @package AppBundle\Assembler
 */
class SettingResponseDTOAssembler
{
    /** @var ConfigurationManager */
    private $configManager;

    /**
     * CurrencyCodeValidator constructor.
     *
     * @param ConfigurationManager $configManager
     */
    public function __construct(ConfigurationManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @param Setting $setting
     * @return SettingResponseDTO
     */
    public function writeDTO(Setting $setting) : SettingResponseDTO
    {
        $configValues = $this->configManager->getConfigValues();
        $configType = $configValues[$setting->getKey()]['type'];
        $configDescription = $configValues[$setting->getKey()]['description'];

        $result = new SettingResponseDTO();
        $result->setKey($setting->getKey());
        $result->setType($configType);
        $result->setValue($setting->getValue());
        $result->setDescription($configDescription);

        return $result;
    }

    /**
     * @param Setting[] $settings
     *
     * @return SettingResponseDTO[]|ArrayCollection
     * @throws \InvalidArgumentException
     */
    public function writeCollectionDTO(array $settings) : ArrayCollection
    {
        $result = new ArrayCollection();

        foreach ($settings as $setting) {
            $result->add($this->writeDTO($setting));
        }

        return $result;
    }
}
