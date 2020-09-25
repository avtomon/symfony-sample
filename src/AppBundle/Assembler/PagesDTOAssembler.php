<?php

namespace AppBundle\Assembler;

use AppBundle\DTO\PagesDTO;

/**
 * Class PagesDTOAssembler
 *
 * @package AppBundle\Assembler
 */
class PagesDTOAssembler
{
    /**
     * @param int $listSize
     * @param int $pageSize
     * @param int $currentPageNumber
     *
     * @return PagesDTO
     */
    public function writeDTO(int $listSize, int $pageSize, int $currentPageNumber) : PagesDTO
    {
        $pageCount = ceil($listSize / $pageSize);

        $pagesDTO = new PagesDTO();
        $pagesDTO->setFirst(1);
        $pagesDTO->setLast($pageCount);
        if ($currentPageNumber > 1 && $currentPageNumber <= $pageCount) {
            $pagesDTO->setPrev($currentPageNumber - 1);
        }

        $pagesDTO->setCurrent($currentPageNumber);
        if ($currentPageNumber < $pageCount) {
            $pagesDTO->setNext($currentPageNumber + 1);
        }

        $pagesDTO->setRowCount($listSize);

        return $pagesDTO;
    }
}
