<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class ManagerInsufficientFundsException
 * @package AppBundle\Exception\Managers
 */
class InsufficientFundsException extends ManagerException
{
    public const MESSAGE = 'Insufficient funds.';
}
