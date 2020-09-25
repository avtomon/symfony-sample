<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerNotFoundException;

/**
 * Class CurrencyNotFoundException
 * @package AppBundle\Exception\Managers\Balance
 */
class CurrencyNotFoundException extends ManagerNotFoundException
{
    public const MESSAGE = 'Currency not found.';
}
