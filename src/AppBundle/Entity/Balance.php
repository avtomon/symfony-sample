<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Balance
 *
 * @ORM\Table(name="billing.balance",
 *     indexes={
 *          @ORM\Index(
 *              name="idx_balance_object_type",
 *              columns={"object_type", "object_id", "type", "currency_code", "account_type"}
 *          )
 *     },
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="uidx_relations_object",
 *              columns={"object_type", "object_id", "type", "currency_code", "account_type"}
 *          )
 *     },
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BalanceRepository")
 */
class Balance
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="object_type", type="string", length=255)
     */
    private $objectType;

    /**
     * @var int
     *
     * @ORM\Column(name="object_id", type="bigint")
     */
    private $objectId;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_code", type="string", length=255)
     */
    private $currencyCode;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="bigint")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="account_type", type="string", columnDefinition="balance_account_types_enum", nullable=true)
     */
    private $accountType;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default": "now()"})
     */
    private $updatedAt;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="balance")
     */
    private $transactions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set objectType.
     *
     * @param string $objectType
     *
     * @return Balance
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * Get objectType.
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Set objectId.
     *
     * @param int $objectId
     *
     * @return Balance
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId.
     *
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set currencyCode.
     *
     * @param string $currencyCode
     *
     * @return Balance
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode.
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Set amount.
     *
     * @param int $amount
     *
     * @return Balance
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get account type
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * Set account type
     *
     * @param string $accountType
     *
     * @return Balance
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Balance
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Balance
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() : \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt) : void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Collection|\AppBundle\Entity\Transaction[]
     */
    public function getProducts(): Collection
    {
        return $this->transactions;
    }


    /**
     * Add transaction.
     *
     * @param \AppBundle\Entity\Transaction $transaction
     *
     * @return Balance
     */
    public function addTransaction(\AppBundle\Entity\Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction.
     *
     * @param \AppBundle\Entity\Transaction $transaction
     *
     * @return boolean TRUE if this collection contained the specified element, false otherwise.
     */
    public function removeTransaction(\AppBundle\Entity\Transaction $transaction)
    {
        return $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
