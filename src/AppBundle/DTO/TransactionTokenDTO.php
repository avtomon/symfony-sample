<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TransactionTokenDTO
 *
 * @package AppBundle\DTO
 *
 * @SWG\Definition(
 *     required={
 *          "transaction_token"
 *     },
 *     type="object",
 *     title="TransactionTokenDTO"
 * )
 */
final class TransactionTokenDTO
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string", groups={"type"})
     *
     * @SWG\Property(property="transaction_token", type="string", example="test")
     */
    private $transactionToken;

    /**
     * @var string
     *
     * @Assert\Type(type="string", groups={"type"})
     *
     * @SWG\Property(property="description", type="string", example="test")
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
