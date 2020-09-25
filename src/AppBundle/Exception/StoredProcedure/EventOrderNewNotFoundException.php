<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerNotFoundException;

/**
 * Class EventOrderNewNotFoundException
 * @package AppBundle\Exception\Managers\Balance
 */
class EventOrderNewNotFoundException extends ManagerNotFoundException
{
    public const MESSAGE = 'Order not found.';
}
