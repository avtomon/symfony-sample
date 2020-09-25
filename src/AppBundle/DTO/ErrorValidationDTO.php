<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;

/**
 * Class ErrorValidationDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(type="object", title="ErrorValidationDTO")
 * @SWG\Property(property="error", type="object", ref="#/definitions/ErrorValidationResultDTO")
 */
class ErrorValidationDTO
{
}
