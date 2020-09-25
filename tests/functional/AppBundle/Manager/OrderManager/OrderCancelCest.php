<?php

namespace Tests\functional\AppBundle\Manager\BonusManager;

use AppBundle\Constants\CurrencyCodes;
use AppBundle\Constants\ObjectTypes;
use AppBundle\Constants\OrderStatus;
use AppBundle\Constants\Settings;
use AppBundle\Constants\TransactionTokenTypes;
use AppBundle\DTO\TransactionTokenDTO;
use AppBundle\Exception\Managers\ManagerNotFoundException;
use Money\Currency;

/**
 * Class OrderCancelCest
 */
class OrderCancelCest
{
    /**
     * @param \FunctionalTester $I
     *
     * @throws \Codeception\Exception\ModuleException
     * @throws \PgFunc\Exception\Usage
     */
    public function okTest(\FunctionalTester $I) : void
    {
        $transactionToken = \uniqid('', true);
        $tradePartnerId = $I->getRandomId();
        $orderId = $I->getRandomId();
        $objectType = ObjectTypes::CONSUMER;

        $objectId = $I->getRandomId();

        $money = $I->getMoneyParser()->parse('100.00', new Currency(CurrencyCodes::USD));

        $I->assertEquals(1e6, (int)$money->getAmount());

        $I->storedProcedure()->balanceRefill(\uniqid('', true), $objectType, $objectId, $money, 'Balance refill');

        $I->storedProcedure()->orderCreate(
            $transactionToken,
            $orderId,
            $tradePartnerId,
            $objectType,
            $objectId,
            $money,
            $description = 'test'
        );

        $transactionTokenId = $I->grabFromDatabase('billing.transaction_token', 'id', [
            'type'  => TransactionTokenTypes::CREATE,
            'token' => $transactionToken,
        ]);
        $I->assertGreaterThan(0, $transactionTokenId, 'Transaction token not found');

        $I->seeInDatabase('billing.event_order', [
            'order_id'      => $orderId,
            'object_type'   => $objectType,
            'object_id'     => $objectId,
            'currency_code' => $money->getCurrency()->getCode(),
            'amount'        => (int)$money->getAmount(),
            'status'        => OrderStatus::NEW,
        ]);

        $orderDTO = new TransactionTokenDTO();
        $orderDTO->setTransactionToken($transactionToken);

        $transactionTokenId = $I->getOrderManager()->orderCancel($orderDTO);
        $I->assertGreaterThan(0, $transactionTokenId);

        $I->seeInDatabase('billing.event_order', [
            'order_id'          => $orderId,
            'object_type'       => $objectType,
            'object_id'         => $objectId,
            'currency_code'     => $money->getCurrency()->getCode(),
            'status'            => OrderStatus::CANCELLED,
            'amount'            => (int)$money->getAmount(),
            'commission_rate'   => Settings::DEFAULT_COMMISSION,
            'transaction_token' => $transactionToken,
        ]);
    }

    /**
     * @param \FunctionalTester $I
     */
    public function error404Test(\FunctionalTester $I) : void
    {
        $transactionToken = \uniqid('', true);

        $orderDTO = new TransactionTokenDTO();
        $orderDTO->setTransactionToken($transactionToken);

        $I->expectThrowable(ManagerNotFoundException::class, static function () use ($I, $orderDTO) {
            $I->getOrderManager()->orderCancel($orderDTO);
        });
    }
}
