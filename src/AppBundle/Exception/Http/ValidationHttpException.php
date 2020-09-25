<?php

namespace AppBundle\Exception\Http;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class ValidationHttpException
 * @package AppBundle\Exception\Http
 */
class ValidationHttpException extends UnprocessableEntityHttpException
{
    /** @var array  */
    private $errors;

    /**
     * ValidationHttpException constructor.
     *
     * @param array $errors
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct(array $errors, $message = 'Validation failed.', \Exception $previous = null)
    {
        $this->errors = $errors;

        parent::__construct($message, $previous);
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
