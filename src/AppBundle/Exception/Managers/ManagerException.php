<?php

namespace AppBundle\Exception\Managers;

/**
 * Class ManagerException
 *
 * @package AppBundle\Exception\Managers
 */
class ManagerException extends \RuntimeException
{
    public const MESSAGE = 'Something went wrong.';

    /**
     * ManagerException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        if (empty($message)) {
            $message = static::MESSAGE;
        }
        parent::__construct($message, $code, $previous);
    }
}
