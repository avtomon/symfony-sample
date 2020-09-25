<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;

/**
 * Class ErrorDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(type="object", title="ErrorDTO")
 * @SWG\Property(property="error", type="object", ref="#/definitions/ErrorResultDTO")
 */
class ErrorDTO
{
}
