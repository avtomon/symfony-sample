<?php

namespace AppBundle\Assembler\InvoicesList;

use AppBundle\DTO\InvoicesList\InvoicesListDTO;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionToken;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;

/**
 * Class InvoicesListAssembler
 */
class InvoicesListAssembler
{
    /** @var MoneyFormatter */
    private $moneyFormatter;

    /**
     * InvoicesListAssembler constructor.
     *
     * @param MoneyFormatter $moneyFormatter
     */
    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @param $amount
     * @param $currencyCode
     *
     * @return float
     */
    private function formatAmount($amount, $currencyCode) : float
    {
        return (float)$this->moneyFormatter->format(
            new Money(
                $amount,
                new Currency($currencyCode)
            )
        );
    }

    /**
     * @param Invoice $invoice
     *
     * @return InvoicesListDTO
     */
    public function writeDTO(Invoice $invoice) : InvoicesListDTO
    {
        $dto = new InvoicesListDTO();

        /** Source data */
        if ($invoice->getSourceBalance()) {
            $dto->setSourceObjectId($invoice->getSourceBalance()->getObjectId());
            $dto->setSourceObjectType($invoice->getSourceBalance()->getObjectType());
            $dto->setSourceBalanceAccountType($invoice->getSourceBalance()->getAccountType());
            $dto->setSourceBalanceType($invoice->getSourceBalance()->getType());
            $dto->setSourceBalanceCurrencyCode($invoice->getSourceBalance()->getCurrencyCode());
            $dto->setSourceAmount(
                $this->formatAmount(
                    $invoice->getSourceAmount(),
                    $invoice->getSourceBalance()->getCurrencyCode()
                )
            );
            if ($invoice->getTransactions()->count()) {
                /** @var Transaction $transaction */
                foreach ($invoice->getTransactions() as $transaction) {
                    if ($transaction->getBalance()
                        && $transaction->getBalance()->getId() !== $invoice->getSourceBalance()->getId()) {
                        continue;
                    }

                    $dto->setSourceBalanceAmount(
                        $this->formatAmount(
                            $transaction->getBalanceAmount(),
                            $invoice->getSourceBalance()->getCurrencyCode()
                        )
                    );
                    break;
                }
            }
        }

        /** Target data */
        if ($invoice->getTargetBalance()) {
            $dto->setTargetObjectId($invoice->getTargetBalance()->getObjectId());
            $dto->setTargetObjectType($invoice->getTargetBalance()->getObjectType());
            $dto->setTargetBalanceAccountType($invoice->getTargetBalance()->getAccountType());
            $dto->setTargetBalanceType($invoice->getTargetBalance()->getType());
            $dto->setTargetBalanceCurrencyCode($invoice->getTargetBalance()->getCurrencyCode());
            $dto->setTargetAmount(
                $this->formatAmount(
                    $invoice->getTargetAmount(),
                    $invoice->getTargetBalance()->getCurrencyCode()
                )
            );
            if ($invoice->getTransactions()->count()) {
                /** @var Transaction $transaction */
                foreach ($invoice->getTransactions() as $transaction) {
                    if ($transaction->getBalance()
                        && $transaction->getBalance()->getId() !== $invoice->getTargetBalance()->getId()) {
                        continue;
                    }

                    $dto->setTargetBalanceAmount(
                        $this->formatAmount(
                            $transaction->getBalanceAmount(),
                            $invoice->getTargetBalance()->getCurrencyCode()
                        )
                    );
                    break;
                }
            }
        }

        /** Order data */
        /** @var  $transactionToken */
        $transactionToken = $invoice->getTransactionToken();
        if ($transactionToken instanceof TransactionToken &&
            isset($transactionToken->getContextRequest()['order_id'])
        ) {
            $dto->setOrderId($transactionToken->getContextRequest()['order_id'] ?? null);
            $dto->setOrderCurrencyCode($transactionToken->getContextRequest()['order_currency_code'] ?? null);
            $dto->setOrderAmount(
                $this->formatAmount(
                    $transactionToken->getContextRequest()['order_amount'] ?? null,
                    $transactionToken->getContextRequest()['order_currency_code'] ?? null
                )
            );
            $dto->setBuyerObjectType($transactionToken->getContextRequest()['object_type'] ?? null);
            $dto->setBuyerObjectId($transactionToken->getContextRequest()['object_id'] ?? null);
            $dto->setOrderStatus($transactionToken->getContextRequest()['order_status'] ?? null);
        }

        /** Invoice data */
        $dto->setDate($invoice->getCreatedAt()->format('c'));
        if ($invoice->getTransactionToken()
            && !empty($invoice->getTransactionToken()->getContextRequest()['description'])) {
            $dto->setDescription($invoice->getTransactionToken()->getContextRequest()['description']);
        }
        $dto->setType($invoice->getType());
        $dto->setDescription($invoice->getDescription());

        return $dto;
    }

    /**
     * @param \Traversable|Invoice[] $invoices
     *
     * @return InvoicesListDTO[]|\Traversable
     */
    public function writeCollection(\Traversable $invoices) : \Traversable
    {
        $result = new ArrayCollection();

        foreach ($invoices as $invoice) {
            $result->add($this->writeDTO($invoice));
        }

        return $result;
    }
}
