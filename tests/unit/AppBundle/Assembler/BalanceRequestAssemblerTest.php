<?php

namespace unit\AppBundle\Result;

use AppBundle\Assembler\BalancesListRequest\BalanceRequestAssembler;
use AppBundle\Assembler\BalancesListRequest\BalanceWhereAssembler;
use AppBundle\SearchContext\Request\BalancesRequest\BalanceRequest;
use AppBundle\SearchContext\Request\BalancesRequest\BalanceWhere;
use Codeception\Test\Unit;

/**
 * Class BalanceRequestAssemblerTest
 *
 * @package unit\AppBundle\Result
 */
class BalanceRequestAssemblerTest extends Unit
{
    /**
     * @throws \Exception
     */
    public function testMain() : void
    {
        /** @var BalanceWhereAssembler $balanceWhereAssembler */
        $balanceWhereAssembler = $this->makeEmpty(BalanceWhereAssembler::class, [
            'writeObject' => static function () {
                return new BalanceWhere();
            },
        ]);

        $balanceRequestAssembler = new BalanceRequestAssembler($balanceWhereAssembler);
        $dummy = $balanceRequestAssembler->writeObject(['limit' => 10, 'sort' => ['id' => 'DESC'], 'page' => 2]);
        $real = new BalanceRequest();
        $real->setSort(['id' => 'DESC']);
        $real->setLimit(10);
        $real->setPage(2);
        $real->setWhere(new BalanceWhere());

        $this->assertEquals($dummy, $real);
    }
}
