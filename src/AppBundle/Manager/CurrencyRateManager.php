<?php

namespace AppBundle\Manager;

use AppBundle\Assembler\CurrencyPairAssembler;
use AppBundle\DTO\CurrencyRateDTO;
use AppBundle\Entity\CurrencyRate;
use AppBundle\Entity\CurrencyRateHistory;
use AppBundle\Exception\Managers\ManagerException;
use Doctrine\Common\Collections\ArrayCollection;
use Exchanger\Exception\UnsupportedExchangeQueryException;
use Swap\Builder;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CurrencyRateManager
 * @package AppBundle\Manager
 */
class CurrencyRateManager extends AbstractManager
{
    /**
     * @var \AppBundle\Repository\CurrencyRateRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    private $currencyRateRepository;

    /**
     * CurrencyRateManager constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     */
    public function __construct(ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $this->currencyRateRepository = $doctrine->getRepository(CurrencyRate::class);
        parent::__construct($doctrine, $validator);
    }

    /**
     * @param CurrencyRateDTO[]|ArrayCollection $rates
     */
    public function updateRates(ArrayCollection $rates) : void
    {
        $dm = $this->doctrine->getManager();

        foreach ($rates as $rate) {
            $this->validate($rate, null, ['type']);
            $this->validate($rate);
            $currencyRate = $this->currencyRateRepository->findOneBy([
                'sourceCurrencyCode' => $rate->getSourceCode(),
                'targetCurrencyCode' => $rate->getTargetCode(),
            ]);
            if ($currencyRate !== null) {
                $multiplier = $currencyRate->getMultiplier();
                $oldRate = $currencyRate->getRate();
                $newRate = $rate->getRate() * $multiplier;

                $currencyRateHistory = new CurrencyRateHistory();
                $currencyRateHistory->setRateNew((int)$newRate);
                $currencyRateHistory->setRateOld((int)$oldRate);
                $currencyRateHistory->setSourceCurrencyCode($currencyRate->getSourceCurrencyCode());
                $currencyRateHistory->setTargetCurrencyCode($currencyRate->getTargetCurrencyCode());
                $dm->persist($currencyRateHistory);

                $currencyRate->setRate((int)$newRate);
                $dm->persist($currencyRate);
            }
        }
        $dm->flush();
    }

    /**
     * @throws ManagerException
     */
    public function importFromCurrencyApi() : void
    {
        try {
            $currencyRates = $this->currencyRateRepository->getSyncableAll();
            $swap = (new Builder())
                ->add('russian_central_bank')
                ->add('fixer', ['access_key' => getenv('FIXER_API_KEY')])
                ->add('currency_layer', ['access_key' => getenv('CURRENCY_LAYER_API_KEY'), 'enterprise' => false])
                ->add('exchange_rates_api')
                ->add('european_central_bank')
                ->add('exchange_rates_api')
                ->add('national_bank_of_romania')
                ->add('central_bank_of_republic_turkey')
                ->add('central_bank_of_czech_republic')
                ->add('russian_central_bank')
                ->add('webservicex')
                ->add('currency_data_feed', ['api_key' => getenv('CURRENCY_DATA_FEED_TOKEN')])
                ->add('currency_converter', [
                    'access_key' => getenv('CURRENCY_CONVERTER_API_KEY'),
                    'enterprise' => false
                ])
                ->add('open_exchange_rates', ['app_id' => getenv('OPEN_EXCHANGE_RATES_APP_ID'), 'enterprise' => false])
                ->build();

            $pairAssembler = new CurrencyPairAssembler();
            $rates = new ArrayCollection();
            /** @var CurrencyRate $currencyRate */
            foreach ($currencyRates as $currencyRate) {
                $pairCode = "{$currencyRate->getSourceCurrencyCode()}/{$currencyRate->getTargetCurrencyCode()}";
                $rate = $swap->latest($pairCode);
                $rates->add($pairAssembler->writeDTO($pairCode, $rate->getValue()));
            }
            $this->updateRates($rates);
        } catch (\Exception $exception) {
            throw new ManagerException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
