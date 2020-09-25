<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class OrderAlreadyFinishedException
 *
 * @package AppBundle\Exception\Managers\Balance
 *
 * @deprecated Never used.
 */
class OrderAlreadyFinishedException extends ManagerException
{
    public const MESSAGE = 'Order already finished.';
}
