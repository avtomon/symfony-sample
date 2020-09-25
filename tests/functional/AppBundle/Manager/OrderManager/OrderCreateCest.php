<?php

namespace functional\AppBundle\Manager\OrderManager;

use AppBundle\Constants\CurrencyCodes;
use AppBundle\Constants\ObjectTypes;
use AppBundle\Constants\OrderStatus;
use AppBundle\Constants\TransactionTokenTypes;
use AppBundle\DTO\OrderDTO;
use AppBundle\Manager\OrderManager;
use Codeception\Util\Stub;
use Money\Currency;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class OrderCreateCest
 *
 * @package functional\AppBundle\Manager\OrderManager
 */
class OrderCreateCest
{

    /**
     * @param \FunctionalTester $I
     *
     * @throws \Codeception\Exception\ModuleException
     * @throws \PgFunc\Exception\Usage
     * @throws \Exception
     */
    public function okTest(\FunctionalTester $I) : void
    {
        /** @var ManagerRegistry $doctrine */
        $doctrine = Stub::makeEmpty(ManagerRegistry::class, []);
        /** @var ValidatorInterface $validator */
        $validator = Stub::makeEmpty(ValidatorInterface::class, []);
        $moneyParser = $I->getMoneyParser();

        $orderManager = new OrderManager(
            $doctrine,
            $validator,
            $I->storedProcedure(),
            $moneyParser
        );
        $orderId = $I->getRandomId();
        $objectId = $I->getRandomId();
        $transactionToken = \uniqid('', true);

        $orderDTO = new OrderDTO();
        $orderDTO->setTransactionToken($transactionToken);
        $orderDTO->setOrderId($orderId);
        $orderDTO->setObjectId($objectId);
        $orderDTO->setObjectType(ObjectTypes::CONSUMER);
        $orderDTO->setTradePartnerId($I->getRandomId());
        $orderDTO->setAmount(12345.00);
        $orderDTO->setCurrencyCode(CurrencyCodes::RUB);
        $orderDTO->setDescription('it is the description');

        $money = $moneyParser->parse((string)$orderDTO->getAmount(), new Currency($orderDTO->getCurrencyCode()));
        $amount = (int)$money->getAmount();

        $I->storedProcedure()->balanceRefill(
            \uniqid('', true),
            ObjectTypes::CONSUMER,
            $objectId,
            $money,
            'Balance refill'
        );

        $orderManager->orderCreate($orderDTO);

        $transactionTokenId = $I->grabFromDatabase('billing.transaction_token', 'id', [
            'type'  => TransactionTokenTypes::CREATE,
            'token' => $transactionToken,
        ]);
        $I->assertGreaterThan(0, $transactionTokenId);

        $criteria = [
            'order_id'          => $orderDTO->getOrderId(),
            'object_type'       => $orderDTO->getObjectType(),
            'object_id'         => $orderDTO->getObjectId(),
            'currency_code'     => $orderDTO->getCurrencyCode(),
            'status'            => OrderStatus::NEW,
            'amount'            => $amount,
            'transaction_token' => $transactionToken,
        ];
        $I->seeInDatabase('billing.event_order', $criteria);
    }
}
