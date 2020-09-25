<?php

namespace AppBundle\Assembler\InvoicesList;

use AppBundle\SearchContext\ApiInvoiceListSearchContext;
use AppBundle\SearchContext\ApiInvoiceListSort;
use AppBundle\SearchContext\ApiInvoiceListWhere;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class InvoicesListRequestAssembler
 */
final class InvoiceListSearchAssembler
{
    /**
     * @param array|null $data
     *
     * @return ApiInvoiceListSort
     */
    private function writeSortFromArray(?array $data) : ApiInvoiceListSort
    {
        $result = new ApiInvoiceListSort();
        if (!$data) {
            return $result;
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($result, $key)) {
                $propertyAccessor->setValue($result, $key, $value);
            }
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return ApiInvoiceListSearchContext
     */
    public function writeSearchContextFromArray($data) : ApiInvoiceListSearchContext
    {
        $result = new ApiInvoiceListSearchContext();

        if (!\is_array($data)) {
            return $result;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if ($propertyAccessor->isReadable($data, '[where]')) {
            $result->setWhere($this->writeWhereFromArray($propertyAccessor->getValue($data, '[where]')));
        }
        if ($propertyAccessor->isReadable($data, '[sort]')) {
            $result->setSort($this->writeSortFromArray($propertyAccessor->getValue($data, '[sort]')));
        }
        if (null !== $propertyAccessor->getValue($data, '[page]')) {
            $result->setPage($propertyAccessor->getValue($data, '[page]'));
        }
        if (null !== $propertyAccessor->getValue($data, '[limit]')) {
            $result->setLimit($propertyAccessor->getValue($data, '[limit]'));
        }

        return $result;
    }

    /**
     * @param array|null $data
     *
     * @return \AppBundle\SearchContext\ApiInvoiceListWhere
     */
    private function writeWhereFromArray(?array $data) : ApiInvoiceListWhere
    {
        $result = new ApiInvoiceListWhere();
        if (!$data) {
            return $result;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($result, $key)) {
                $propertyAccessor->setValue($result, $key, $value);
            }
        }

        if ($result->getDateFrom() !== null) {
            $dateTime = null;
            try {
                $dateTime = new \DateTimeImmutable($data['date_from']);
            } catch (\Exception $e) {
            }

            $result->setDateFromAsDateTime($dateTime);
        }

        if ($result->getDateTo() !== null) {
            $dateTime = null;
            try {
                $dateTime = (new \DateTimeImmutable($data['date_to']))->modify('+1 day');
            } catch (\Exception $e) {
            }

            $result->setDateToAsDateTime($dateTime);
        }

        return $result;
    }
}
