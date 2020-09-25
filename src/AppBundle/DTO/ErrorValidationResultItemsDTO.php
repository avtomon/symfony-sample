<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;

/**
 * Class ErrorResultItemsDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(type="object", title="ErrorResultItemsDTO", required={"field", "message"})
 * @SWG\Property(property="field", type="string")
 * @SWG\Property(property="message", type="string")
 */
class ErrorValidationResultItemsDTO
{
}
