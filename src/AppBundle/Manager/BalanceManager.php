<?php

namespace AppBundle\Manager;

use AppBundle\Assembler\BalanceResponseDTOAssembler;
use AppBundle\Assembler\BalancesListResponse\BalanceListItemDTOAssembler;
use AppBundle\Doctrine\StoredProcedureManager;
use AppBundle\DTO\BalanceRefillOrChargeOffRequestDTO;
use AppBundle\DTO\BalanceRequestDTO;
use AppBundle\Entity\Balance;
use AppBundle\SearchContext\Request\BalancesRequest\BalanceRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use PgFunc\Exception\Usage;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class BalanceManager
 * @package AppBundle\Manager
 */
class BalanceManager extends AbstractManager
{
    /** @var StoredProcedureManager */
    private $storedProcedure;

    /** @var BalanceResponseDTOAssembler */
    private $balanceResponseDTOAssembler;

    /** @var BalanceListItemDTOAssembler */
    private $balanceListItemDTOAssembler;

    /**@var DecimalMoneyParser */
    private $moneyParser;

    /**
     * BalanceManager constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     * @param BalanceResponseDTOAssembler $balanceResponseDTOAssembler
     * @param BalanceListItemDTOAssembler $balanceListItemDTOAssembler
     * @param StoredProcedureManager $storedProcedure
     * @param DecimalMoneyParser $moneyParser
     */
    public function __construct(
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        BalanceResponseDTOAssembler $balanceResponseDTOAssembler,
        BalanceListItemDTOAssembler $balanceListItemDTOAssembler,
        StoredProcedureManager $storedProcedure,
        DecimalMoneyParser $moneyParser
    )
    {
        $this->storedProcedure = $storedProcedure;
        $this->balanceResponseDTOAssembler = $balanceResponseDTOAssembler;
        $this->balanceListItemDTOAssembler = $balanceListItemDTOAssembler;
        $this->moneyParser = $moneyParser;

        parent::__construct($doctrine, $validator);
    }

    /**
     * Получить список балансов
     *
     * @param \AppBundle\DTO\BalanceRequestDTO $balanceRequestDTO
     *
     * @return ArrayCollection
     * @throws \InvalidArgumentException
     */
    public function getBalancesByObject(BalanceRequestDTO $balanceRequestDTO) : ArrayCollection
    {
        $this->validate($balanceRequestDTO, null, ['type']);
        $this->validate($balanceRequestDTO);

        /** @var \AppBundle\Repository\BalanceRepository $balanceRepository */
        $balanceRepository = $this->doctrine->getRepository(Balance::class);

        $result = $this->balanceResponseDTOAssembler->writeCollectionDTO(
            $balanceRepository->findByObject(
                $balanceRequestDTO->getObjectType(),
                $balanceRequestDTO->getObjectId()
            )
        );

        return $result;
    }

    /**
     * @param BalanceRequest $balanceRequest
     *
     * @return \AppBundle\DTO\BalanceResponseDTO[]|ArrayCollection
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function getBalancesList(BalanceRequest $balanceRequest)
    {
        $this->validate($balanceRequest, null, ['type']);
        $this->validate($balanceRequest);
        $this->validate($balanceRequest->getWhere(), null, ['type']);
        $this->validate($balanceRequest->getWhere());

        /** @var \AppBundle\Repository\BalanceRepository $balanceRepository */
        $balanceRepository = $this->doctrine->getRepository(Balance::class);

        $rawBalances = $balanceRepository->getData($balanceRequest);
        $balances = [];
        foreach ($rawBalances as $rawBalance) {
            $balance = new Balance();
            $balance->setObjectType($rawBalance['object_type']);
            $balance->setObjectId($rawBalance['object_id']);
            $balance->setCurrencyCode($rawBalance['currency_code']);
            $balance->setAccountType($rawBalance['account_type']);
            $balance->setType($rawBalance['type']);
            $balance->setAmount($rawBalance['amount']);
            $balance->setUpdatedAt(new \DateTime($rawBalance['updated_at']));

            $balances[] = $balance;
        }

        return $this->balanceListItemDTOAssembler->writeCollectionDTO($balances);
    }

    /**
     * @param BalanceRequest $balanceRequest
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getBalancesListCount(BalanceRequest $balanceRequest) : int
    {
        $this->validate($balanceRequest, null, ['type']);
        $this->validate($balanceRequest);
        $this->validate($balanceRequest->getWhere(), null, ['type']);
        $this->validate($balanceRequest->getWhere());

        /** @var \AppBundle\Repository\BalanceRepository $balanceRepository */
        $balanceRepository = $this->doctrine->getRepository(Balance::class);

        return $balanceRepository->getCount($balanceRequest);
    }

    /**
     * Refill current personal balance of object
     *
     * @param BalanceRefillOrChargeOffRequestDTO $dto
     *
     * @throws Usage
     */
    public function balanceRefill(BalanceRefillOrChargeOffRequestDTO $dto) : void
    {
        $this->validate($dto, null, ['type']);
        $this->validate($dto);

        $money = $this->moneyParser->parse((string)$dto->getAmount(), new Currency($dto->getCurrencyCode()));

        $this->storedProcedure->balanceRefill(
            $dto->getTransactionToken(),
            $dto->getObjectType(),
            $dto->getObjectId(),
            $money,
            $dto->getDescription()
        );
    }

    /**
     * Charge off current personal balance of object
     *
     * @param BalanceRefillOrChargeOffRequestDTO $dto
     *
     * @throws Usage
     */
    public function balanceChargeOff(BalanceRefillOrChargeOffRequestDTO $dto) : void
    {
        $this->validate($dto, null, ['type']);
        $this->validate($dto);

        $money = $this->moneyParser->parse((string)$dto->getAmount(), new Currency($dto->getCurrencyCode()));

        $this->storedProcedure->balanceChargeOff(
            $dto->getTransactionToken(),
            $dto->getObjectType(),
            $dto->getObjectId(),
            $money,
            $dto->getDescription()
        );
    }
}
