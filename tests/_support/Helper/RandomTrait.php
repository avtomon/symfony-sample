<?php

namespace Tests\_support\Helper;

use AppBundle\Constants\CurrencyCodes;

/**
 * Trait RandomTrait.
 */
trait RandomTrait
{
    /**
     * @return int
     *
     * @throws \Exception
     */
    public function getRandomQuantity() : int
    {
        return random_int(1, 1000);
    }

    /**
     * @param int $min
     * @param int $max
     * @return int
     * @throws \Exception
     */
    public function getRandomPositiveInt(int $min = 1, int $max = 100000) : int
    {
        return random_int($min, $max);
    }

    /**
     * @param int $min
     * @param int $max
     * @return float
     * @throws \Exception
     */
    public function getRandomFloat(int $min = 1, int $max = 100000) : float
    {
        return (float)($this->getRandomPositiveInt($min, $max) . '.' . random_int(1, 99));
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getRandomCurrencyCode() : string
    {
        $index = random_int(0, count(CurrencyCodes::ALL_CODES) - 1);

        return CurrencyCodes::ALL_CODES[$index];
    }

    /**
     * @throws \Exception
     *
     * @return \DateTimeImmutable
     */
    public function getRandomFutureDateTime() : \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->setTimestamp(time() + random_int(300, 3.154e+7));
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getRandomFutureDateTimeAsString() : string
    {
        return $this->getRandomFutureDateTime()->format('Y-m-d H:i:s');
    }
}
