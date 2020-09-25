<?php

namespace unit\AppBundle\Result;

use AppBundle\Result\PaginationResult;
use AppBundle\SearchContext\SearchContextInterface;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class PaginationResultTest
 *
 * @package unit\AppBundle\Result
 */
class PaginationResultTest extends Unit
{
    /**
     * @throws \Exception
     */
    public function testGetPages1() : void
    {
        /** @var SearchContextInterface $searchContext */
        $searchContext = $this->makeEmpty(SearchContextInterface::class, [
            'getPage'  => static function () {
                return 1;
            },
            'getLimit' => static function () {
                return 2;
            },
        ]);

        $count = 5;
        $list = new ArrayCollection();
        $paginationResult = new PaginationResult($list, $count, $searchContext);

        $this->assertEquals($paginationResult->getPages(), [
            'first'     => 1,
            'last'      => 3,
            'next'      => 2,
            'prev'      => null,
            'row_count' => 5,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function testGetPages2() : void
    {
        /** @var SearchContextInterface $searchContext */
        $searchContext = $this->makeEmpty(SearchContextInterface::class, [
            'getPage'  => static function () {
                return 5;
            },
            'getLimit' => static function () {
                return 10;
            },
        ]);

        $count = 151;
        $list = new ArrayCollection();
        $paginationResult = new PaginationResult($list, $count, $searchContext);

        $this->assertEquals($paginationResult->getPages(), [
            'first'     => 1,
            'last'      => 16,
            'next'      => 6,
            'prev'      => 4,
            'row_count' => $count,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function testGetResult() : void
    {
        /** @var SearchContextInterface $searchContext */
        $searchContext = $this->makeEmpty(SearchContextInterface::class, [
            'getPage'  => static function () {
                return 1;
            },
            'getLimit' => static function () {
                return 2;
            },
        ]);

        $count = 5;
        $list = new ArrayCollection([
            ['id' => 1,],
            ['id' => 2,],
            ['id' => 3,],
            ['id' => 4,],
            ['id' => 5,],
        ]);
        $paginationResult = new PaginationResult($list, $count, $searchContext);

        $this->assertEquals($paginationResult->getResult(), $list);
    }

    /**
     * @throws \Exception
     */
    public function testJsonSerialize() : void
    {
        /** @var SearchContextInterface $searchContext */
        $searchContext = $this->makeEmpty(SearchContextInterface::class, [
            'getPage'  => static function () {
                return 1;
            },
            'getLimit' => static function () {
                return 2;
            },
        ]);

        $count = 5;
        $list = new ArrayCollection([
            ['id' => 1,],
            ['id' => 2,],
            ['id' => 3,],
            ['id' => 4,],
            ['id' => 5,],
        ]);
        $paginationResult = new PaginationResult($list, $count, $searchContext);

        $this->assertEquals(json_encode($paginationResult), json_encode([
            'result' => $list,
            'pages'  => [
                'first'     => 1,
                'last'      => 3,
                'next'      => 2,
                'prev'      => null,
                'row_count' => 5,
            ],
        ]));
    }
}
