<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

class TransactionTokenPaidException extends ManagerException
{
    public const MESSAGE = 'Transaction token paid.';
}
