<?php

namespace Tests\functional\AppBundle\Manager\BalanceManager;

use AppBundle\Constants\BalanceAccountTypes;
use AppBundle\Constants\BalanceTypes;
use AppBundle\Constants\CurrencyCodes;
use AppBundle\Constants\InvoiceTypes;
use AppBundle\Constants\ObjectTypes;
use AppBundle\Constants\TransactionTokenTypes;
use AppBundle\DTO\BalanceRefillOrChargeOffRequestDTO;
use AppBundle\Exception\StoredProcedure\TransactionTokenExistsException;
use Codeception\Exception\ModuleException;
use PgFunc\Exception\Usage;

/**
 * Class OrderCancelCest
 */
class BalanceRefillCest
{
    /**
     * @param \FunctionalTester $I
     *
     * @throws ModuleException
     * @throws Usage
     * @throws \Doctrine\DBAL\DBALException
     */
    public function okTest(\FunctionalTester $I) : void
    {
        $I->clearState();

        $objectType = ObjectTypes::CONSUMER;
        $objectId = $I->getRandomId();
        $amountRefill = 550.0;
        $transactionToken = uniqid('', true);

        $dto = new BalanceRefillOrChargeOffRequestDTO();
        $dto->setObjectId($objectId);
        $dto->setObjectType($objectType);
        $dto->setAmount($amountRefill);
        $dto->setCurrencyCode(CurrencyCodes::RUB);
        $dto->setTransactionToken($transactionToken);
        $dto->setDescription('test');

        $I->getBalanceManager()->balanceRefill($dto);

        $transactionTokenId = $I->grabFromDatabase('billing.transaction_token', 'id', [
            'type'  => TransactionTokenTypes::CREATE,
            'token' => $transactionToken,
        ]);
        $I->assertGreaterThan(0, $transactionTokenId);

        $writeOffBalanceId = $I->grabFromDatabase('billing.balance', 'id', [
            'object_type'   => ObjectTypes::SYSTEM,
            'object_id'     => 0,
            'type'          => BalanceTypes::WRITE_OFF,
            'currency_code' => CurrencyCodes::RUB,
            'account_type'  => BalanceAccountTypes::PERSONAL,
            'amount'        => 0,
        ]);
        $I->assertGreaterThan(0, $writeOffBalanceId);

        $awaitingBalanceId = $I->grabFromDatabase('billing.balance', 'id', [
            'object_type'   => $objectType,
            'object_id'     => $objectId,
            'type'          => BalanceTypes::AWAITING,
            'currency_code' => CurrencyCodes::RUB,
            'account_type'  => BalanceAccountTypes::PERSONAL,
            'amount'        => 0,
        ]);
        $I->assertGreaterThan(0, $awaitingBalanceId);

        $sourceCurrentBalanceId = $I->grabFromDatabase('billing.balance', 'id', [
            'object_type'   => ObjectTypes::SYSTEM,
            'object_id'     => 0,
            'type'          => BalanceTypes::CURRENT,
            'currency_code' => CurrencyCodes::RUB,
            'account_type'  => BalanceAccountTypes::PERSONAL,
            'amount'        => -$amountRefill * 1e4,
        ]);
        $I->assertGreaterThan(0, $sourceCurrentBalanceId);

        $targetCurrentBalanceId = $I->grabFromDatabase('billing.balance', 'id', [
            'object_type'   => $objectType,
            'object_id'     => $objectId,
            'type'          => BalanceTypes::CURRENT,
            'currency_code' => CurrencyCodes::RUB,
            'account_type'  => BalanceAccountTypes::PERSONAL,
            'amount'        => $amountRefill * 1e4,
        ]);
        $I->assertGreaterThan(0, $targetCurrentBalanceId);

        $I->seeInDatabase('billing.invoice', [
            'source_balance_id'    => $writeOffBalanceId,
            'target_balance_id'    => $awaitingBalanceId,
            'type'                 => InvoiceTypes::FUTURE_PAYMENT,
            'source_amount'        => $amountRefill * 1e4,
            'target_amount'        => $amountRefill * 1e4,
            'transaction_token_id' => $transactionTokenId,
            'event_order_id'       => null,
        ]);

        $I->seeInDatabase('billing.invoice', [
            'source_balance_id'    => $awaitingBalanceId,
            'target_balance_id'    => $writeOffBalanceId,
            'type'                 => InvoiceTypes::COMPLETE,
            'source_amount'        => $amountRefill * 1e4,
            'target_amount'        => $amountRefill * 1e4,
            'transaction_token_id' => $transactionTokenId,
            'event_order_id'       => null,
        ]);

        $I->seeInDatabase('billing.invoice', [
            'source_balance_id'    => $sourceCurrentBalanceId,
            'target_balance_id'    => $targetCurrentBalanceId,
            'type'                 => InvoiceTypes::FUTURE_PAYMENT_COMPLETE,
            'source_amount'        => $amountRefill * 1e4,
            'target_amount'        => $amountRefill * 1e4,
            'transaction_token_id' => $transactionTokenId,
            'event_order_id'       => null,
        ]);
    }

    /**
     * @param \FunctionalTester $I
     *
     * @throws ModuleException
     * @throws Usage
     */
    public function transactionTokenExistsTest(\FunctionalTester $I) : void
    {
        $dto = new BalanceRefillOrChargeOffRequestDTO();
        $dto->setObjectId($I->getRandomId());
        $dto->setObjectType(ObjectTypes::CONSUMER);
        $dto->setAmount(550.0);
        $dto->setCurrencyCode(CurrencyCodes::USD);
        $dto->setTransactionToken(uniqid('', true));
        $dto->setDescription('test');

        $I->getBalanceManager()->balanceRefill($dto);

        $I->expectThrowable(TransactionTokenExistsException::class, static function () use ($I, $dto) {
            $I->getBalanceManager()->balanceRefill($dto);
        });
    }
}
