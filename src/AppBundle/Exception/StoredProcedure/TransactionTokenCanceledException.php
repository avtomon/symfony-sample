<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class TransactionTokenCanceledException
 * @package AppBundle\Exception\Managers\Balance
 */
class TransactionTokenCanceledException extends ManagerException
{
    public const MESSAGE = 'Transaction token canceled.';
}
