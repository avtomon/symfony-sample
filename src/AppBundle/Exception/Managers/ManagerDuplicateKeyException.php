<?php

namespace AppBundle\Exception\Managers;

/**
 * Class ManagerDuplicateKeyException
 *
 * @package AppBundle\Exception\Managers
 */
class ManagerDuplicateKeyException extends ManagerException
{
    public const MESSAGE = 'Record already present.';
}
