<?php

namespace functional\AppBundle\Manager\BalanceManager;

use AppBundle\Constants\BalanceAccountTypes;
use AppBundle\Constants\BalanceTypes;
use AppBundle\Constants\ObjectTypes;
use AppBundle\DTO\BalanceRequestDTO;
use AppBundle\DTO\BalanceResponseDTO;

/**
 * Class BalanceManagerTest
 */
class GetBalanceCest
{
    /**
     * @param \FunctionalTester $I
     *
     * @throws \Codeception\Exception\ModuleException
     * @throws \PgFunc\Exception
     * @throws \PgFunc\Exception\Usage
     * @throws \Exception
     */
    public function okTest(\FunctionalTester $I) : void
    {
        $objectId = $I->getRandomId();
        $objectType = ObjectTypes::CONSUMER;
        $balanceType = BalanceTypes::AWAITING;
        $currencyCode = 'USD';
        $accountType = BalanceAccountTypes::PERSONAL;

        $sp = $I->storedProcedure();
        $balanceId = $sp->getOrCreateBalance(
            $objectType,
            $objectId,
            $accountType,
            $balanceType,
            $currencyCode
        );

        $I->seeInDatabase('billing.balance', [
            'id'            => $balanceId,
            'object_type'   => $objectType,
            'object_id'     => $objectId,
            'currency_code' => $currencyCode,
            'type'          => $balanceType,
            'account_type'  => $accountType,
        ]);

        $balanceRequestDTO = new BalanceRequestDTO();
        $balanceRequestDTO->setObjectType($objectType);
        $balanceRequestDTO->setObjectId($objectId);

        $balances = $I->getBalanceManager()->getBalancesByObject($balanceRequestDTO);

        $I->assertCount(1, $balances);
        $I->assertInstanceOf(BalanceResponseDTO::class, $balances[0]);
        $I->assertEquals($balanceId, $balances[0]->getId());
    }
}
