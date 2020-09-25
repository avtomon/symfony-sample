<?php

namespace AppBundle\DTO;

use AppBundle\Validator\Constraints as AppAssert;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OrderDTO
 * @package AppBundle\DTO
 *
 * @SWG\Definition(
 *     required={
 *          "transaction_token",
 *          "order_id",
 *          "trade_partner_id",
 *          "object_type",
 *          "object_id",
 *          "currency_code",
 *          "amount",
 *          "description"
 *     },
 *     type="object",
 *     title="OrderDTO"
 * )
 */
final class OrderDTO
{

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string", groups={"type"})
     *
     * @SWG\Property(property="transaction_token", type="string")
     */
    private $transactionToken;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="integer")
     *
     * @SWG\Property(property="order_id", type="integer")
     */
    private $orderId;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="integer")
     *
     * @SWG\Property(property="trade_partner_id", type="integer")
     */
    private $tradePartnerId;

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
     * @SWG\Property(
     *     property="object_type",
     *     type="string",
     *     enum=\AppBundle\Constants\ObjectTypes::ALL_TYPES,
     *     example="consumer"
     * )
     */
    private $objectType;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="integer")
     *
     * @SWG\Property(property="object_id", type="integer", example=20)
     */
    private $objectId;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     * @AppAssert\CurrencyCode()
     *
     * @SWG\Property(property="currency_code", type="string" , example="USD")
     */
    private $currencyCode;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="float")
     * @Assert\GreaterThanOrEqual(0.0001)
     *
     * @SWG\Property(property="amount", type="float", example=150.2)
     */
    private $amount;

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
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param $orderId
     */
    public function setOrderId($orderId) : void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getObjectType() : string
    {
        return $this->objectType;
    }

    /**
     * @param string $objectType
     */
    public function setObjectType(string $objectType) : void
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
     * @return string
     */
    public function getCurrencyCode() : string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode(string $currencyCode) : void
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     */
    public function setAmount($amount) : void
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getTradePartnerId()
    {
        return $this->tradePartnerId;
    }

    /**
     * @param $tradePartnerId
     */
    public function setTradePartnerId($tradePartnerId) : void
    {
        $this->tradePartnerId = $tradePartnerId;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription($description) : void
    {
        $this->description = $description;
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
