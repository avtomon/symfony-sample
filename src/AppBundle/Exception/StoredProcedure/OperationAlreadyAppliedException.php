<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class OperationAlreadyAppliedException
 *
 * @package AppBundle\Exception\Managers\Balance
 *
 * @deprecated Never used.
 */
class OperationAlreadyAppliedException extends ManagerException
{
    public const MESSAGE = 'Operation already applied.';
}
