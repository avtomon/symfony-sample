<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class OrderNotPaidException
 */
class OrderNotPaidException extends ManagerException
{
    public const MESSAGE = 'Order not paid.';
}
