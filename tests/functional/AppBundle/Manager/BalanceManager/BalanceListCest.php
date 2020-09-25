<?php

namespace functional\AppBundle\Manager\BalanceManager;

use AppBundle\Constants\BalanceAccountTypes;
use AppBundle\Constants\BalanceTypes;
use AppBundle\Constants\CurrencyCodes;
use AppBundle\DTO\BalancesList\BalanceBriefDTO;
use AppBundle\DTO\BalancesList\BalanceListItemResponseDTO;
use AppBundle\Exception\Managers\ManagerValidationException;
use AppBundle\SearchContext\Constants\SortDirections;
use AppBundle\SearchContext\Request\BalancesRequest\BalanceRequest;
use AppBundle\SearchContext\Request\BalancesRequest\BalanceWhere;
use Money\Currency;

/**
 * Class BalanceListCest
 *
 * @package functional\AppBundle\Manager\BalanceManager
 */
class BalanceListCest
{
    /**
     * @param \FunctionalTester $I
     *
     * @throws \Codeception\Exception\ModuleException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PgFunc\Exception\Usage
     * @throws \Exception
     */
    public function okTest(\FunctionalTester $I) : void
    {
        $balanceManager = $I->getBalanceManager();
        $money = $I->getMoneyParser();

        $objectType1 = $I->getRandomObjectType();
        $objectId1 = $I->getRandomId();

        $type2 = BalanceTypes::SUSPENDED;
        $accountType2 = BalanceAccountTypes::PERSONAL;
        $amount2 = $money->parse('100', new Currency(CurrencyCodes::RUB));
        $I->accrueBalance(
            $objectType1,
            $objectId1,
            $accountType2,
            $type2,
            $amount2
        );

        $balanceRequestWhere = new BalanceWhere();
        $balanceRequestWhere->setObjectType($objectType1);
        $balanceRequestWhere->setObjectId($objectId1);
        $balanceRequestWhere->setType([BalanceTypes::SUSPENDED]);
        $balanceRequestWhere->setAccountType(
            [
                BalanceAccountTypes::PERSONAL,
            ]
        );
        $balanceRequestWhere->setCurrencyCode([CurrencyCodes::RUB,]);
        $balanceRequestWhere->setDateFromAsDateTime(
            (new \DateTimeImmutable('2018-10-10'))->modify('+1 day')
        );
        $balanceRequestWhere->setAmountFromAsMoney($amount2);

        $balanceListRequest = new BalanceRequest();
        $balanceListRequest->setWhere($balanceRequestWhere);
        $balanceListRequest->setSort(['id' => SortDirections::DESC]);
        $balanceListRequest->setLimit(10);
        $balanceListRequest->setPage(1);

        $result = $balanceManager->getBalancesList($balanceListRequest);

        $assertBalanceBrief2 = new BalanceBriefDTO();
        $assertBalanceBrief2->setCurrencyCode(CurrencyCodes::RUB);
        $assertBalanceBrief2->setAccountType($accountType2);
        $assertBalanceBrief2->setType($type2);
        $assertBalanceBrief2->setAmount($amount2->getAmount() / 1e4);

        $updatedAt = $I->grabFromDatabase(
            'billing.balance',
            'updated_at',
            [
                'object_id'     => $objectId1,
                'object_type'   => $objectType1,
                'type'          => $type2,
                'currency_code' => CurrencyCodes::RUB,
                'account_type'  => $accountType2,
            ]
        );
        $assertBalanceBrief2->setUpdatedAt((new \DateTime($updatedAt))->format('c'));

        $assertBalanceResult = new BalanceListItemResponseDTO();
        $assertBalanceResult->setObjectId($objectId1);
        $assertBalanceResult->setObjectType($objectType1);
        $assertBalanceResult->addBalance($assertBalanceBrief2);

        $I->assertEquals([$assertBalanceResult], $result->toArray());

        $balanceListRequest->setSort(['id' => strtolower(SortDirections::DESC)]);

        $result = $balanceManager->getBalancesList($balanceListRequest);
        $I->assertEquals([$assertBalanceResult], $result->toArray());
    }

    /**
     * @param \FunctionalTester $I
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function simpleValidationExceptionTest(\FunctionalTester $I) : void
    {
        $balanceManager = $I->getBalanceManager();

        $objectType = 'fdskljfdklsjfklds';
        $objectId = $I->getRandomId();

        $balanceRequestWhere = new BalanceWhere();
        $balanceRequestWhere->setObjectType($objectType);
        $balanceRequestWhere->setObjectId($objectId);
        $balanceRequestWhere->setType([BalanceTypes::AWAITING]);
        $balanceRequestWhere->setAccountType(
            [
                BalanceAccountTypes::PERSONAL,
            ]
        );
        $balanceRequestWhere->setCurrencyCode([CurrencyCodes::USD,]);

        $balanceListRequest = new BalanceRequest();
        $balanceListRequest->setWhere($balanceRequestWhere);
        $balanceListRequest->setSort(['id' => SortDirections::DESC]);
        $balanceListRequest->setLimit(10);
        $balanceListRequest->setPage(1);

        $I->expectThrowable(ManagerValidationException::class, static function () use (
            $balanceListRequest,
            $balanceManager
        ) {
            $balanceManager->getBalancesList($balanceListRequest);
        });
    }
}
