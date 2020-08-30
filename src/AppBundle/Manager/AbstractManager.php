<?php

namespace AppBundle\Manager;

use AppBundle\Exception\Managers\ManagerValidationException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AbstractManager
 * @package AppBundle\Manager
 */
abstract class AbstractManager
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var ObjectManager */
    protected $objectManager;

    /**
     * RelationManager constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     */
    public function __construct(ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $this->doctrine = $doctrine;
        $this->objectManager = $doctrine->getManager();
        $this->validator = $validator;
    }

    /**
     * Validation of object
     *
     * @param object|array $object
     *
     * @param Constraint|null $constraint
     * @param null $groups
     *
     * @return bool
     */
    protected function validate($object, Constraint $constraint = null, $groups = null) : bool
    {
        $converter = new CamelCaseToSnakeCaseNameConverter();
        $msgErrors = [];

        try {
            /** @var ConstraintViolationList|ConstraintViolation[] $errors */
            $errors = $this->validator->validate($object, $constraint, $groups);
        } catch (UnexpectedTypeException $e) {
            throw new ManagerValidationException(
                $msgErrors,
                $e->getMessage() . ' Use groups for separate your tests (https://stackoverflow.com/a/13587162).'
            );
        }
        if (null !== $errors && $errors->count() > 0) {
            foreach ($errors as $err) {
                $msgErrors[] = [
                    'field'   => $converter->normalize($err->getPropertyPath()),
                    'message' => $err->getMessage(),
                ];
            }

            throw new ManagerValidationException($msgErrors);
        }

        return true;
    }
}
