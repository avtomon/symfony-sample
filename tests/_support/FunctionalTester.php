<?php

use AppBundle\Constants\BalanceAccountTypes;
use AppBundle\Constants\BalanceTypes;
use AppBundle\Constants\CurrencyCodes;
use AppBundle\Constants\ObjectTypes;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * @return int
     */
    public function getRandomId() : int
    {
        usleep(1000);
        return (int)(microtime(1) * 1e6);
    }

    /**
     * @return string
     */
    public function getRandomObjectType() : string
    {
        return array_rand(
            array_flip(
                [
                    ObjectTypes::CONSUMER,
                    ObjectTypes::TRADE_PARTNER,
                ]
            )
        );
    }

    /**
     * @param $sourceCurrencyCode string
     * @param $targetCurrencyCode string
     * @param $amount float|int
     *
     * @return float|int
     */
    public function convertCurrency($sourceCurrencyCode, $targetCurrencyCode, $amount)
    {
        $currencyRate = $this->grabFromDatabase(
            'billing.currency_rate',
            'rate',
            [
                'source_currency_code' => $sourceCurrencyCode,
                'target_currency_code' => $targetCurrencyCode,
            ]
        );

        $rateMultiplier = $this->grabFromDatabase(
            'billing.currency_rate',
            'multiplier',
            [
                'source_currency_code' => $sourceCurrencyCode,
                'target_currency_code' => $targetCurrencyCode,
            ]
        );

        return $amount * $currencyRate / $rateMultiplier;
    }

    /**
     * Возвращает значение колонки, если запись найдена, в противном случае - дефолтное значение
     *
     * @param string $table
     * @param string $column
     * @param array $condition
     * @param $valueDefault
     *
     * @return mixed
     */
    public function grabValueFromDataBaseOrDefault(string $table, string $column, $valueDefault, array $condition)
    {
        $value = $valueDefault;

        $rowCount = $this->grabNumRecords($table, $condition);

        $this->assertLessOrEquals(1, $rowCount);

        if ($rowCount === 1) {
            $value = $this->grabFromDatabase($table, $column, $condition);
        }

        return $value;
    }

    /**
     * Возвращает состояние баланса для system
     *
     * @param string $currencyCode
     * @param string $type
     * @param string $accountType
     *
     * @return mixed
     */
    public function getAmountSystemBalance(
        string $currencyCode,
        string $type = BalanceTypes::CURRENT,
        string $accountType = BalanceAccountTypes::PERSONAL
    )
    {
        $condition = [
            'object_type'   => ObjectTypes::SYSTEM,
            'object_id'     => 0,
            'currency_code' => $currencyCode,
            'type'          => $type,
            'account_type'  => $accountType,
        ];

        return $this->grabFromDatabase('billing.balance', 'amount', $condition);
    }

    /**
     * Возвращает ID баланса
     *
     * @param string $objectType
     * @param int $objectId
     * @param string $currencyCode
     * @param string $type
     * @param string $accountType
     *
     * @return array
     */
    public function getBalanceId(
        string $objectType,
        int $objectId,
        string $currencyCode,
        string $type,
        string $accountType
    ) : array
    {
        $condition = [
            'object_type'   => $objectType,
            'object_id'     => $objectId,
            'currency_code' => $currencyCode,
            'type'          => $type,
            'account_type'  => $accountType,
        ];

        return $this->grabFromDatabase('billing.balance', 'id', $condition);
    }
}
