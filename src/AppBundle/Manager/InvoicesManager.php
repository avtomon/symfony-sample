<?php

namespace AppBundle\Manager;

use AppBundle\Assembler\InvoicesList\InvoicesListAssembler;
use AppBundle\Entity\Invoice;
use AppBundle\Result\PaginationResult;
use AppBundle\SearchContext\ApiInvoiceListSearchContext;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class InvoicesManager
 */
class InvoicesManager extends AbstractManager
{
    /**
     * @var InvoicesListAssembler
     */
    private $invoicesListAssembler;

    /**
     * InvoicesManager constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     * @param InvoicesListAssembler $invoicesListAssembler
     */
    public function __construct(
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        InvoicesListAssembler $invoicesListAssembler
    )
    {
        $this->invoicesListAssembler = $invoicesListAssembler;

        parent::__construct($doctrine, $validator);
    }

    /**
     * @param ApiInvoiceListSearchContext $context
     *
     * @return PaginationResult
     */
    public function invoicesList(ApiInvoiceListSearchContext $context) : PaginationResult
    {
        $this->validate($context, null, ['type']);
        $this->validate($context);

        $repository = $this->doctrine->getRepository(Invoice::class);

        $paginator = $repository->findByContext($context);
        $collection = $this->invoicesListAssembler->writeCollection($paginator);

        return new PaginationResult($collection, $paginator->count(), $context);
    }
}
