<?php

namespace Tests\functional\AppBundle\Manager\BonusManager;

use AppBundle\Constants\CurrencyCodes;
use AppBundle\Constants\ObjectTypes;
use AppBundle\Constants\OrderStatus;
use AppBundle\Constants\TransactionTokenTypes;
use AppBundle\DTO\OrderPaidDTO;
use AppBundle\DTO\TransactionTokenDTO;
use AppBundle\Exception\Managers\ManagerNotFoundException;
use Money\Currency;

/**
 * Class OrderCompleteCest
 * @package Tests\functional\AppBundle\Manager\BonusManager
 */
class OrderCompleteCest
{
    /**
     * @param \FunctionalTester $I
     *
     * @throws \Codeception\Exception\ModuleException
     * @throws \PgFunc\Exception\Usage
     */
    public function okTest(\FunctionalTester $I) : void
    {
        $tradePartnerId = $I->getRandomId();
        $orderId = $I->getRandomId();
        $objectType = ObjectTypes::CONSUMER;
        $currencyCode = CurrencyCodes::RUB;
        $objectId1 = $I->getRandomId();

        $moneyParser = $I->getMoneyParser();
        $money = $moneyParser->parse('100.00', new Currency($currencyCode));

        $token = \uniqid('', true);

        $I->assertEquals(1e6, (int)$money->getAmount());

        $I->storedProcedure()->balanceRefill(\uniqid('', true), $objectType, $objectId1, $money, 'Balance refill');

        // Создали заказ
        $I->storedProcedure()->orderCreate(
            $token,
            $orderId,
            $tradePartnerId,
            $objectType,
            $objectId1,
            $money,
            'test_order'
        );

        $transactionTokenCreateId = $I->grabFromDatabase('billing.transaction_token', 'id', [
            'type'  => TransactionTokenTypes::CREATE,
            'token' => $token,
        ]);
        $I->assertGreaterThan(0, $transactionTokenCreateId, 'Transaction token not found');

        $I->seeInDatabase('billing.event_order', [
            'order_id'      => $orderId,
            'object_type'   => $objectType,
            'object_id'     => $objectId1,
            'currency_code' => $money->getCurrency()->getCode(),
            'amount'        => (int)$money->getAmount(),
            'status'        => OrderStatus::NEW,
        ]);

        // Оплатили заказ
        $dto = new OrderPaidDTO();
        $dto->setTransactionToken($token);
        $dto->setIsCashPayment(false);
        $dto->setDescription('order paid');

        $I->getOrderManager()->orderPaid($dto);

        $transactionTokenPaidId = $I->grabFromDatabase('billing.transaction_token', 'id', [
            'type'  => TransactionTokenTypes::PAID,
            'token' => $token,
        ]);
        $I->assertGreaterThan(0, $transactionTokenPaidId, 'Transaction token not found');

        // Закрыли заказ (complete)
        $dto = new TransactionTokenDTO();
        $dto->setTransactionToken($token);

        $I->getOrderManager()->orderCompleted($dto);
        $transactionTokenCompleteId = $I->grabFromDatabase('billing.transaction_token', 'id', [
            'type'  => TransactionTokenTypes::COMPLETE,
            'token' => $token,
        ]);
        $I->assertGreaterThan(0, $transactionTokenCompleteId, 'Transaction token not found');

        $I->seeInDatabase('billing.event_order', [
            'order_id'      => $orderId,
            'object_type'   => $objectType,
            'object_id'     => $objectId1,
            'currency_code' => $money->getCurrency()->getCode(),
            'amount'        => (int)$money->getAmount(),
            'status'        => OrderStatus::COMPLETED,
        ]);
    }

    /**
     * @param \FunctionalTester $I
     */
    public function error404Test(\FunctionalTester $I) : void
    {
        $dto = new TransactionTokenDTO();
        $dto->setTransactionToken(\uniqid('', true));

        $I->expectThrowable(ManagerNotFoundException::class, static function () use ($I, $dto) {
            $I->getOrderManager()->orderCompleted($dto);
        });
    }
}
