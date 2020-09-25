<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OrderPaidDTO
 *
 * @package AppBundle\DTO
 *
 * @SWG\Definition(required={
 *     "transaction_token",
 *     "is_cash_payment",
 *     "description"
 * }, type="object", title="OrderPaidDTO")
 */
class OrderPaidDTO
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     *
     * @SWG\Property(property="transaction_token", type="string", example="test")
     */
    private $transactionToken;

    /**
     * @var bool
     *
     * @Assert\Type(type="bool")
     * @Assert\NotNull()
     *
     * @SWG\Property(property="is_cash_payment", type="bool", example=false)
     */
    private $isCashPayment;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     *
     * @SWG\Property(property="description", type="string", example="The order was generated automatically")
     */
    private $description;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) : void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isCashPayment()
    {
        return $this->isCashPayment;
    }

    /**
     * @param bool $isCashPayment
     */
    public function setIsCashPayment($isCashPayment) : void
    {
        $this->isCashPayment = $isCashPayment;
    }

    /**
     * @return string
     */
    public function getTransactionToken()
    {
        return $this->transactionToken;
    }

    /**
     * @param string $transactionToken
     */
    public function setTransactionToken($transactionToken) : void
    {
        $this->transactionToken = $transactionToken;
    }
}
