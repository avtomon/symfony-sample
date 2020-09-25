<?php

namespace acceptance\Balance;

use AppBundle\Constants\BalanceAccountTypes;
use AppBundle\Constants\BalanceTypes;
use AppBundle\Constants\CurrencyCodes;
use AppBundle\Constants\ObjectTypes;
use Codeception\Util\HttpCode;

/**
 * Class BalanceListCest
 *
 * @package acceptance\Balance
 */
class BalanceListCest
{
    /**
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function okTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $objectType = ObjectTypes::CONSUMER;
        $objectId1 = $I->getRandomObjectId();
        $objectId2 = $I->getRandomObjectId();
        $amount1 = 500.5;
        $amount2 = 100.0;
        $amount3 = 200.5;
        $currencyCode1 = $I->getRandomCurrencyCode();
        $currencyCode2 = $I->getRandomCurrencyCode();
        $transactionToken1 = uniqid('', true);
        $transactionToken2 = uniqid('', true);
        $transactionToken3 = uniqid('', true);

        $I->sendPOST('balances_refill', [
            'object_type'       => $objectType,
            'object_id'         => $objectId1,
            'currency_code'     => $currencyCode1,
            'amount'            => $amount1,
            'transaction_token' => $transactionToken1,
            'description'       => 'test',
        ]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $I->sendPOST('balances_refill', [
            'object_type'       => $objectType,
            'object_id'         => $objectId2,
            'currency_code'     => $currencyCode2,
            'amount'            => $amount2,
            'transaction_token' => $transactionToken2,
            'description'       => 'test',
        ]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $I->sendPOST('balances_refill', [
            'object_type'       => ObjectTypes::CONSUMER,
            'object_id'         => $objectId2,
            'currency_code'     => $currencyCode2,
            'amount'            => $amount3,
            'transaction_token' => $transactionToken3,
            'description'       => 'test',
        ]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $I->sendPOST('balances_list', [
            'where' => [
                'object_type'   => ObjectTypes::CONSUMER,
                'object_id'     => $objectId2,
                'currency_code' => [$currencyCode2,],
                'type'          => [
                    BalanceTypes::AWAITING,
                    BalanceTypes::CURRENT,
                ],
                'account_type'  => [
                    BalanceAccountTypes::PERSONAL,
                ],
                'date_from'     => '2018-10-10',
                'amount_from'   => $amount2,
            ],
            'sort'  => [
                'updated_at' => 'desc',
                'id'         => 'ASC',
            ],
            'limit' => 1,
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'result' => [
                [
                    'object_type' => ObjectTypes::CONSUMER,
                    'object_id'   => $objectId2,
                    'balances'    => [
                        [
                            'currency_code' => $currencyCode2,
                            'account_type'  => BalanceAccountTypes::PERSONAL,
                            'type'          => BalanceTypes::CURRENT,
                            'amount'        => $amount2 + $amount3,
                        ],
                    ],
                ],
            ],
            'pages'  => [
                'first'     => 1,
                'last'      => 1,
                'prev'      => null,
                'current'   => 1,
                'next'      => null,
                'row_count' => 1,
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorInvalidObjectTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'object_type' => 'lalala',
                'object_id'   => 'test',
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'object_type',
                        'message' => 'The value you selected is not a valid choice.',
                    ],
                    [
                        'field'   => 'object_id',
                        'message' => 'This value should be of type integer.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorMissingDependencyTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'object_id' => 100,
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'object_type',
                        'message' => 'Object ID required indicated type of object.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorFailedSortTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'object_type' => ObjectTypes::CONSUMER,
                'object_id'   => 100,
            ],
            'sort'  => [
                'test' => 'test',
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'sort[test]',
                        'message' => 'This field was not expected.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorFailedPageTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'object_type' => ObjectTypes::CONSUMER,
                'object_id'   => 100,
            ],
            'page'  => 'test',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'page',
                        'message' => 'This value should be of type integer.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorFailedLimitTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'object_type' => ObjectTypes::CONSUMER,
                'object_id'   => 100,
            ],
            'limit' => 1000,
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'limit',
                        'message' => 'This value should be less than or equal to 100.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorFailedDatesTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'date_from' => '2018.01.01',
                'date_to'   => '2025-10-01',
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'date_from',
                        'message' => 'This value is not a valid date.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorDatesLessThanDefaultTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'date_from' => '2010-01-01',
                'date_to'   => '2010-01-01',
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'date_from',
                        'message' => 'This value should be greater than "2018-01-01".',
                    ],
                    [
                        'field'   => 'date_to',
                        'message' => 'This value should be greater than "2018-01-01".',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorInvalidDatesRangeTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'date_from' => '2018-01-10',
                'date_to'   => '2018-01-01',
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
            'error' => [
                'message' => 'Validation failed.',
                'errors'  => [
                    [
                        'field'   => 'date_from',
                        'message' => 'Start date of date range must be less than end date of range.',
                    ],
                    [
                        'field'   => 'date_to',
                        'message' => 'End date of date range must be greater than start date of range.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorFailedAmountsTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'amount_from' => 100,
                'amount_to'   => 20000,
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
                'error' => [
                    'message' => 'Validation failed.',
                    'errors'  => [
                        [
                            'field'   => 'amount_from',
                            'message' => 'This value should be of type float.',
                        ],
                        [
                            'field'   => 'currency_code',
                            'message' => 'Start amount of amount range required currency code.',
                        ],
                        [
                            'field'   => 'amount_to',
                            'message' => 'This value should be of type float.',
                        ],
                        [
                            'field'   => 'currency_code',
                            'message' => 'End amount of amount range required currency code.',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorInvalidAmountsRangeTest(\AcceptanceTester $I) : void
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_list', [
            'where' => [
                'currency_code' => [CurrencyCodes::HUF,],
                'amount_from'   => 1000.0,
                'amount_to'     => 200.0,
            ],
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseContainsJson([
                'error' => [
                    'message' => 'Validation failed.',
                    'errors'  => [
                        [
                            'field'   => 'amount_from',
                            'message' => 'Start amount of amount range must be less than end amount of range.',
                        ],
                        [
                            'field'   => 'amount_to',
                            'message' => 'End amount of amount range must be greater than start date of range.',
                        ],
                    ],
                ],
            ]);
    }
}
