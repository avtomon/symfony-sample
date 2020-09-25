<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TransactionToken
 *
 * @ORM\Table(
 *     name="billing.transaction_token",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="uidx_transaction_token_type_token",
 *              columns={"type", "token"}
 *          )
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionTokenRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TransactionToken
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
     * @ORM\Column(name="token", type="text")
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", columnDefinition="transaction_token_types_enum", nullable=true)
     */
    private $type;

    /**
     * @var Collection|Invoice[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Invoice", mappedBy="transactionToken")
     */
    private $invoices;

    /**
     * @var array
     *
     * @ORM\Column(name="context_request", type="json", nullable=true)
     */
    private $contextRequest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * Set token.
     *
     * @param string $token
     *
     * @return TransactionToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return TransactionToken
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
     * @return TransactionToken
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
     * Constructor
     */
    public function __construct()
    {
        $this->invoices = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add invoice.
     *
     * @param \AppBundle\Entity\Invoice $invoice
     *
     * @return TransactionToken
     */
    public function addInvoice(\AppBundle\Entity\Invoice $invoice)
    {
        $this->invoices[] = $invoice;

        return $this;
    }

    /**
     * Remove invoice.
     *
     * @param \AppBundle\Entity\Invoice $invoice
     *
     * @return boolean TRUE if this collection contained the specified element, false otherwise.
     */
    public function removeInvoice(\AppBundle\Entity\Invoice $invoice)
    {
        return $this->invoices->removeElement($invoice);
    }

    /**
     * Get invoices.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @return array
     */
    public function getContextRequest() : array
    {
        return $this->contextRequest ?? [];
    }

    /**
     * @param array $contextRequest
     */
    public function setContextRequest(array $contextRequest) : void
    {
        $this->contextRequest = $contextRequest;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }
}
