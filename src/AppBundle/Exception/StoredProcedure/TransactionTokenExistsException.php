<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class TransactionTokenExistsException
 * @package AppBundle\Exception\Managers\Balance
 */
class TransactionTokenExistsException extends ManagerException
{
    public const MESSAGE = 'Transaction token already created.';
}
