<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

class CurrencyUnprocessableException extends ManagerException
{
    public const MESSAGE = 'Unprocessable currency.';
}
