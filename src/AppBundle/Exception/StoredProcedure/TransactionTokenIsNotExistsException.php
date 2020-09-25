<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerNotFoundException;

/**
 * Class TransactionTokenIsNotExistsException
 * @package AppBundle\Exception\Managers\Balance
 */
class TransactionTokenIsNotExistsException extends ManagerNotFoundException
{
    public const MESSAGE = 'Transaction token is not created.';
}
