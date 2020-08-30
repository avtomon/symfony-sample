<?php

namespace AppBundle\Manager;

use AppBundle\Doctrine\StoredProcedureManager;
use AppBundle\DTO\OrderDTO;
use AppBundle\DTO\OrderPaidDTO;
use AppBundle\DTO\TransactionTokenDTO;
use Money\Currency;
use Money\MoneyParser;
use PgFunc\Exception\Usage;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class OrderManager
 * @package AppBundle\Manager
 */
class OrderManager extends AbstractManager
{
    /**
     * @var StoredProcedureManager
     */
    private $storedProcedure;

    /**
     * @var MoneyParser
     */
    private $moneyParser;

    /**
     * OrderManager constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     * @param StoredProcedureManager $storedProcedure
     * @param MoneyParser $moneyParser
     */
    public function __construct(
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        StoredProcedureManager $storedProcedure,
        MoneyParser $moneyParser
    )
    {
        $this->storedProcedure = $storedProcedure;
        $this->moneyParser = $moneyParser;

        parent::__construct($doctrine, $validator);
    }

    /**
     * Create order
     *
     * @param OrderDTO $orderDTO
     *
     * @throws Usage
     */
    public function orderCreate(OrderDTO $orderDTO) : void
    {
        $this->validate($orderDTO, null, ['type']);
        $this->validate($orderDTO);

        $money = $this->moneyParser->parse((string)$orderDTO->getAmount(), new Currency($orderDTO->getCurrencyCode()));

        $this->storedProcedure->orderCreate(
            $orderDTO->getTransactionToken(),
            $orderDTO->getOrderId(),
            $orderDTO->getTradePartnerId(),
            $orderDTO->getObjectType(),
            $orderDTO->getObjectId(),
            $money,
            $orderDTO->getDescription()
        );
    }

    /**
     * @param TransactionTokenDTO $dto
     *
     * @throws Usage
     */
    public function orderCompleted(TransactionTokenDTO $dto) : void
    {
        $this->validate($dto, null, ['type']);
        $this->validate($dto);

        $this->storedProcedure->orderCompleted($dto->getTransactionToken(), $dto->getDescription());
    }

    /**
     * @param TransactionTokenDTO $dto
     *
     * @return int|array
     *
     * @throws Usage
     */
    public function orderCancel(TransactionTokenDTO $dto)
    {
        $this->validate($dto, null, ['type']);
        $this->validate($dto);

        return $this->storedProcedure->orderCancel($dto->getTransactionToken(), $dto->getDescription());
    }

    /**
     * @param OrderPaidDTO $dto
     *
     * @return int|null
     *
     * @throws Usage
     */
    public function orderPaid(OrderPaidDTO $dto) : ?int
    {
        $this->validate($dto, null, ['type']);
        $this->validate($dto);

        return $this->storedProcedure->orderPaid(
            $dto->getTransactionToken(),
            $dto->isCashPayment(),
            $dto->getDescription()
        );
    }
}
