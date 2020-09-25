<?php

namespace AppBundle\Assembler;

use AppBundle\DTO\BalanceResponseDTO;
use AppBundle\Entity\Balance;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * Класс для формирование BalanceDTO с float значением валюты
 */
class BalanceResponseDTOAssembler
{
    /**
     * @var \Money\Formatter\DecimalMoneyFormatter
     */
    private $decimalMoneyFormatter;

    /**
     * Constructor
     * @param \Money\Formatter\DecimalMoneyFormatter $decimalMoneyFormatter
     */
    public function __construct(DecimalMoneyFormatter $decimalMoneyFormatter)
    {
        $this->decimalMoneyFormatter = $decimalMoneyFormatter;
    }

    /**
     * @param Balance $balance
     *
     * @return BalanceResponseDTO
     * @throws \InvalidArgumentException
     */
    private function writeDTO(Balance $balance) : BalanceResponseDTO
    {
        $money = $this->decimalMoneyFormatter->format(
            new Money($balance->getAmount(), new Currency($balance->getCurrencyCode()))
        );

        $result = new BalanceResponseDTO();
        $result->setId($balance->getId());
        $result->setObjectType($balance->getObjectType());
        $result->setObjectId($balance->getObjectId());
        $result->setCurrencyCode($balance->getCurrencyCode());
        $result->setAmount($money);
        $result->setAccountType($balance->getAccountType());
        $result->setType($balance->getType());

        return $result;
    }

    /**
     * @param Balance[] $balances
     *
     * @return BalanceResponseDTO[]|ArrayCollection
     * @throws \InvalidArgumentException
     */
    public function writeCollectionDTO(array $balances) : ArrayCollection
    {
        $result = new ArrayCollection();

        foreach ($balances as $balance) {
            $result->add($this->writeDTO($balance));
        }

        return $result;
    }
}
