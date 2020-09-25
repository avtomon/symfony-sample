<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class ManagerInvalidOrderException
 * @package AppBundle\Exception\Managers
 */
class InvalidInvoiceTypeException extends ManagerException
{
    public const MESSAGE = 'Invalid invoice type.';
}
