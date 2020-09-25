<?php

namespace acceptance\DefaultController;

use Codeception\Util\HttpCode;

/**
 * Class IndexCest
 *
 * @package acceptance\DefaultController
 */
class IndexCest
{
    /**
     * @param \AcceptanceTester $I
     */
    public function okTest(\AcceptanceTester $I) : void
    {
        $I->sendGET(getenv('TEST_URL'));
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'result' => [
                'project' => 'Billing',
                'status' => 'Ok',
            ],
        ]);
    }
}
