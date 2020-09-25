<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;

/**
 * Class ErrorResultDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(type="object", title="ErrorResultDTO")
 * @SWG\Property(property="message", type="string")
 * @SWG\Property(property="errors", type="array", items=@SWG\Items(ref="#/definitions/ErrorValidationResultItemsDTO"))
 */
class ErrorValidationResultDTO
{
}
