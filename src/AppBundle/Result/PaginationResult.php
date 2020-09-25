<?php

namespace AppBundle\Result;

use AppBundle\Constants\Pagination;
use AppBundle\SearchContext\SearchContextInterface;

/**
 * Class Result
 */
class PaginationResult implements \JsonSerializable
{
    /** @var SearchContextInterface */
    private $searchContext;

    /** @var \Traversable */
    private $list;

    /** @var int */
    private $count;

    /**
     * PaginationResult constructor.
     *
     * @param \Traversable $list
     * @param int $count
     * @param SearchContextInterface $searchContext
     */
    public function __construct(\Traversable $list, int $count, SearchContextInterface $searchContext)
    {
        $this->list = $list;
        $this->count = $count;
        $this->searchContext = $searchContext;
    }

    /**
     * @return \Traversable
     */
    public function getResult() : \Traversable
    {
        return $this->list;
    }

    /**
     * @return array
     */
    public function getPages() : array
    {
        $last = ceil($this->count / $this->searchContext->getLimit()) ?: Pagination::PAGE_DEFAULT;
        $next = $this->searchContext->getPage() + 1;
        $prev = $this->searchContext->getPage() - 1;
        return [
            'first' => Pagination::PAGE_DEFAULT,
            'last' => $last,
            'next' => $next > $last ? null : $next,
            'prev' => $prev < Pagination::PAGE_DEFAULT ? null : $prev,
            'row_count' => $this->count,
        ];
    }

    /**
     * Interface implementation
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'result' => $this->getResult(),
            'pages' => $this->getPages(),
        ];
    }
}
