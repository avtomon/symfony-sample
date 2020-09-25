<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;

/**
 * Class SettingResponseDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(required={"key", "value", "type", "description"}, type="object", title="SettingRequestDTO")
 */
class SettingResponseDTO
{
    /**
     * @var string
     *
     * @SWG\Property(property="key", type="string")
     */
    private $key;

    /**
     * @var string|int|float|array
     *
     * @SWG\Property(property="value", description="Any valid json")
     */
    private $value;

    /**
     * @var string
     *
     * @SWG\Property(property="type", type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @SWG\Property(property="description", type="string")
     */
    private $description;

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key) : void
    {
        $this->key = $key;
    }

    /**
     * @return array|float|int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array|float|int|string|object $value
     */
    public function setValue($value) : void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param $type
     */
    public function setType($type) : void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }
}
