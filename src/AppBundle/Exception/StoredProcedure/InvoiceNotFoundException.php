<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerNotFoundException;

/**
 * Class InvoiceNotFoundException
 * @package AppBundle\Exception\Managers
 */
class InvoiceNotFoundException extends ManagerNotFoundException
{
    public const MESSAGE = 'Invoice not found.';
}
