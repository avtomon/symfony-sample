<?php

use Codeception\Util\HttpCode;

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
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * @return int
     */
    public function getRandomObjectId() : int
    {
        usleep(1000);
        return (int)(microtime(true) * 1e6);
    }

    /**
     * @param AcceptanceTester $I
     * @param string $objectType
     * @param int $objectId
     * @param float $commissionRate
     */
    public function createObjectData(
        \AcceptanceTester $I,
        string $objectType,
        int $objectId,
        float $commissionRate = 0.13
    ) : void
    {
        $I->sendPOST('object_data_create', [
            'object_id'       => $objectId,
            'object_type'     => $objectType,
            'commission_rate' => $commissionRate,
        ]);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseContainsJson([
            'object_id'       => $objectId,
            'object_type'     => $objectType,
            'commission_rate' => $commissionRate,
        ]);
    }
}
