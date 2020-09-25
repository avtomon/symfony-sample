<?php

namespace acceptance\Balance;

use AppBundle\Constants\CurrencyCodes;
use AppBundle\Constants\ObjectTypes;
use AppBundle\Exception\StoredProcedure\TransactionTokenExistsException;
use Codeception\Util\HttpCode;

/**
 * Class BalanceChargeOffCest
 *
 * @package acceptance\Balance
 */
class BalanceChargeOffCest
{
    /**
     * @param \AcceptanceTester $I
     */
    public function okTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150.01,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorIdIsStringTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => '15',
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150.01,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'object_id', 'message' => 'This value should be of type int.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorIdIsFloatTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => 15.0,
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150.01,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'object_id', 'message' => 'This value should be of type int.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorObjectTypeNotValidTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => 'lalala',
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150.01,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'object_type', 'message' => 'The value you selected is not a valid choice.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorCurrencyCodeNotValidTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => 'Lalala',
            'amount'            => 150.00,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'currency_code', 'message' => 'The value you selected is not a valid choice.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorAmountIsStringTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => '150.04',
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'amount', 'message' => 'This value should be of type float.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorAmountIsIntegerTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'amount', 'message' => 'This value should be of type float.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorAmountIsZeroTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::TRADE_PARTNER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 0.0,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'amount', 'message' => 'This value should be greater than or equal to 0.0001.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorAmountIsNegativeTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => -150.01,
            'transaction_token' => uniqid('', true),
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'amount', 'message' => 'This value should be greater than or equal to 0.0001.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorTokenNotStringTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::TRADE_PARTNER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150.01,
            'transaction_token' => 15,
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'transaction_token', 'message' => 'This value should be of type string.',],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationErrorTokenExistTest(\AcceptanceTester $I) : void
    {
        $transactionToken = uniqid('', true);

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150.01,
            'transaction_token' => $transactionToken,
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 11330.01,
            'transaction_token' => $transactionToken,
            'description'       => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => TransactionTokenExistsException::MESSAGE,
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function validationDescriptionIsIntTest(\AcceptanceTester $I) : void
    {
        $transactionToken = uniqid('', true);

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_charge_off', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $I->getRandomObjectId(),
            'currency_code'     => CurrencyCodes::RUB,
            'amount'            => 150.01,
            'transaction_token' => $transactionToken,
            'description'       => 12,

        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    ['field' => 'description', 'message' => 'This value should be of type string.',],
                ],
            ],
        ]);
    }
}
