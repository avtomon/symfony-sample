<?php

namespace AppBundle\Exception\StoredProcedure;

use AppBundle\Exception\Managers\ManagerException;

/**
 * Class CannotLoadSettingsException
 * @package AppBundle\Exception\Managers\Balance
 */
class CannotLoadSettingsException extends ManagerException
{
    public const MESSAGE = 'Cannot load settings.';
}
