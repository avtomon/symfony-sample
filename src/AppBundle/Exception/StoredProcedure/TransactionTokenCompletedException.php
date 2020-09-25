<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class TransactionTokenCompletedException
 * @package AppBundle\Exception\Managers\Balance
 */
class TransactionTokenCompletedException extends ManagerException
{
    public const MESSAGE = 'Transaction token completed.';
}
