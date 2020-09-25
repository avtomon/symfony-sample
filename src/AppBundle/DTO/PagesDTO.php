<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;

/**
 * Class PagesDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(required={"first", "last", "prev", "current", "next", "count"},
 *                 type="object", title="PagesDTO")
 */
class PagesDTO
{
    /**
     * @var int
     *
     * @SWG\Property(property="first", type="integer")
     */
    private $first;

    /**
     * @var int
     *
     * @SWG\Property(property="last", type="integer")
     */
    private $last;

    /**
     * @var int|null
     *
     * @SWG\Property(property="prev", type="integer")
     */
    private $prev;

    /**
     * @var int
     *
     * @SWG\Property(property="current", type="integer")
     */
    private $current;

    /**
     * @var int|null
     *
     * @SWG\Property(property="next", type="integer")
     */
    private $next;

    /**
     * @var int
     *
     * @SWG\Property(property="row_count", type="integer")
     */
    private $rowCount;

    /**
     * @return int
     */
    public function getFirst() : int
    {
        return $this->first;
    }

    /**
     * @param int $first
     */
    public function setFirst(int $first) : void
    {
        $this->first = $first;
    }

    /**
     * @return int
     */
    public function getLast() : int
    {
        return $this->last;
    }

    /**
     * @param int $last
     */
    public function setLast(int $last) : void
    {
        $this->last = $last;
    }

    /**
     * @return int|null
     */
    public function getPrev() : ?int
    {
        return $this->prev;
    }

    /**
     * @param int $prev
     */
    public function setPrev(int $prev) : void
    {
        $this->prev = $prev;
    }

    /**
     * @return int
     */
    public function getCurrent() : int
    {
        return $this->current;
    }

    /**
     * @param int $current
     */
    public function setCurrent(int $current) : void
    {
        $this->current = $current;
    }

    /**
     * @return int|null
     */
    public function getNext() : ?int
    {
        return $this->next;
    }

    /**
     * @param int $next
     */
    public function setNext(int $next) : void
    {
        $this->next = $next;
    }

    /**
     * @return int
     */
    public function getRowCount() : int
    {
        return $this->rowCount;
    }

    /**
     * @param int $rowCount
     */
    public function setRowCount(int $rowCount) : void
    {
        $this->rowCount = $rowCount;
    }
}
