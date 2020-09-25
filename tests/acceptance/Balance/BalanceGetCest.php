<?php

namespace acceptance\Balance;

use AppBundle\Constants\ObjectTypes;
use Codeception\Util\HttpCode;

/**
 * Class BalanceGetCest
 *
 * @package acceptance\Balance
 */
class BalanceGetCest
{
    /**
     * @param \AcceptanceTester $I
     */
    public function okTest(\AcceptanceTester $I) : void
    {
        $objectType = ObjectTypes::CONSUMER;
        $objectId = $I->getRandomObjectId();

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_get', [
            'object_type' => $objectType,
            'object_id'   => $objectId,
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson(['result' => []]);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorBadObjectTypeNotValidTest(\AcceptanceTester $I) : void
    {
        $objectType = ObjectTypes::CONSUMER;
        $objectId = 'llala';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_get', [
            'object_type' => $objectType,
            'object_id'   => $objectId,
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function validationErrorBadObjectIdNotValidTest(\AcceptanceTester $I) : void
    {
        $objectType = 'lalala';
        $objectId = $I->getRandomObjectId();

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('balances_get', [
            'object_type' => $objectType,
            'object_id'   => $objectId,
        ]);

        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }
}
