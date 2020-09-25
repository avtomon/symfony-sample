<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ObjectDataDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(
 *     required={"object_type", "object_id", "commission_rate"},
 *     type="object",
 *     title="ObjectDataDTO"
 * )
 */
class ObjectDataDTO
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     * @Assert\Choice(
     *     strict=true,
     *     choices=\AppBundle\Constants\ObjectTypes::ALL_TYPES
     * )
     *
     * @SWG\Property(property="object_type", type="string", enum=\AppBundle\Constants\ObjectTypes::ALL_TYPES)
     */
    private $objectType;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="int")
     *
     * @SWG\Property(property="object_id", type="integer")
     */
    private $objectId;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="float")
     * @Assert\Range(
     *     min=0,
     *     max=1,
     *     minMessage="This value must be between 0 and 1",
     *     maxMessage="This value must be between 0 and 1"
     * )
     * @Assert\Regex(pattern="/^[0-9]+([.][0-9]{1,4})?$/", message="Format of value must be 0.1111")
     *
     * @SWG\Property(property="commission_rate", type="float", example=0.2)
     */
    private $commissionRate;

    /**
     * @var boolean
     *
     * @Assert\Type(type="boolean")
     *
     * @SWG\Property(property="is_default", type="boolean", example=true)
     */
    private $isDefault;

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @param string $objectType
     */
    public function setObjectType($objectType) : void
    {
        $this->objectType = $objectType;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param $objectId
     */
    public function setObjectId($objectId) : void
    {
        $this->objectId = $objectId;
    }

    /**
     * @return float
     */
    public function getCommissionRate()
    {
        return $this->commissionRate;
    }

    /**
     * @param float $commissionRate
     */
    public function setCommissionRate($commissionRate) : void
    {
        $this->commissionRate = $commissionRate;
    }

    /**
     * @return bool
     */
    public function getIsDefault() : bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     */
    public function setIsDefault(bool $isDefault) : void
    {
        $this->isDefault = $isDefault;
    }
}
