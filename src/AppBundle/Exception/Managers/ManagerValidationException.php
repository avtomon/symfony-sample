<?php

namespace AppBundle\Exception\Managers;

/**
 * Class RelationManagerValidationException
 * @package AppBundle\Exception\Relation
 */
class ManagerValidationException extends ManagerException
{
    public const MESSAGE = 'Validation failed.';

    /** @var array */
    private $errors;

    public function __construct(array $errors = [], string $message = '')
    {
        $this->errors = $errors;

        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
