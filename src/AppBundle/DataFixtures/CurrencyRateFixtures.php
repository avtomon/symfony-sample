<?php

namespace AppBundle\DataFixtures;

use AppBundle\Constants\CurrencyCodes;
use AppBundle\Entity\Currency;
use AppBundle\Entity\CurrencyRate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

/**
 * Фикстура для курсов валют
 */
class CurrencyRateFixtures extends Fixture
{
    /**
     * @param ObjectManager $dm
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function load(ObjectManager $dm) : void
    {
        $currencyList = [
            ['code' => CurrencyCodes::GBP, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::RUB, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::EUR, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::USD, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::CNY, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::TRY, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::CHF, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::HUF, 'multiplier' => 10000,],
            ['code' => CurrencyCodes::BGN, 'multiplier' => 10000,],
        ];

        foreach ($currencyList as $currencyItem) {
            $currency = new Currency();
            $currency->setCurrencyCode($currencyItem['code']);
            $currency->setMultiplier($currencyItem['multiplier']);
            $dm->persist($currency);
        }
        $dm->flush();

        $currencyRateList = [
            [
                'source'     => CurrencyCodes::GBP,
                'target'     => CurrencyCodes::USD,
                'rate'       => 2,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::GBP,
                'rate'       => 0.5,
                'multiplier' => 10000,
            ],

            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::RUB,
                'rate'       => 50,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::RUB,
                'target'     => CurrencyCodes::USD,
                'rate'       => 0.02,
                'multiplier' => 10000,
            ],

            [
                'source'     => CurrencyCodes::EUR,
                'target'     => CurrencyCodes::USD,
                'rate'       => 2,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::EUR,
                'rate'       => 2,
                'multiplier' => 10000,
            ],

            [
                'source'     => CurrencyCodes::CNY,
                'target'     => CurrencyCodes::USD,
                'rate'       => 0.1,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::CNY,
                'rate'       => 100,
                'multiplier' => 10000,
            ],

            [
                'source'     => CurrencyCodes::TRY,
                'target'     => CurrencyCodes::USD,
                'rate'       => 0.2,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::TRY,
                'rate'       => 50,
                'multiplier' => 10000,
            ],

            [
                'source'     => CurrencyCodes::CHF,
                'target'     => CurrencyCodes::USD,
                'rate'       => 1,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::CHF,
                'rate'       => 1,
                'multiplier' => 10000,
            ],

            [
                'source'     => CurrencyCodes::HUF,
                'target'     => CurrencyCodes::USD,
                'rate'       => 0.005,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::HUF,
                'rate'       => 200,
                'multiplier' => 10000,
            ],

            [
                'source'     => CurrencyCodes::BGN,
                'target'     => CurrencyCodes::USD,
                'rate'       => 0.5,
                'multiplier' => 10000,
            ],
            [
                'source'     => CurrencyCodes::USD,
                'target'     => CurrencyCodes::BGN,
                'rate'       => 2,
                'multiplier' => 10000,
            ],
        ];

        foreach ($currencyRateList as $currencyRate) {
            $currencyRateObject = new CurrencyRate();

            $currencyRateObject->setSourceCurrencyCode($currencyRate['source']);
            $currencyRateObject->setTargetCurrencyCode($currencyRate['target']);
            $currencyRateObject->setMultiplier($currencyRate['multiplier']);

            $roundRate = (int) (((float) $currencyRate['rate']) * $currencyRate['multiplier']);
            $currencyRateObject->setRate($roundRate);

            $dm->persist($currencyRateObject);
        }

        $dm->flush();

        /** @var EntityManager $dm */
        $dm->getConnection()->exec(
            'INSERT INTO billing.currency_rate(source_currency_code, target_currency_code, rate, multiplier) 
             SELECT c1.currency_code, c2.currency_code, 10000, 10000
             FROM billing.currency c1, billing.currency c2
             WHERE c1.currency_code != c2.currency_code
             ON CONFLICT DO NOTHING'
        );
    }
}
