<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BankTransactionRepository")
 */
class BankTransaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $order_id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total_amount;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $paid_amount;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $bank;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $transaction_status;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expire_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paid_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qr_link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qr_string;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getOrderId(): ?int
    {
        return $this->order_id;
    }

    public function setOrderId(?int $order_id): self
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->total_amount;
    }

    public function setTotalAmount(?float $total_amount): self
    {
        $this->total_amount = $total_amount;

        return $this;
    }

    public function getPaidAmount(): ?float
    {
        return $this->paid_amount;
    }

    public function setPaidAmount(?float $paid_amount): self
    {
        $this->paid_amount = $paid_amount;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBank(): ?string
    {
        return $this->bank;
    }

    public function setBank(?string $bank): self
    {
        $this->bank = $bank;

        return $this;
    }

    public function getTransactionStatus(): ?string
    {
        return $this->transaction_status;
    }

    public function setTransactionStatus(?string $transaction_status): self
    {
        $this->transaction_status = $transaction_status;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expire_at;
    }

    public function setExpireAt(?\DateTimeInterface $expire_at): self
    {
        $this->expire_at = $expire_at;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paid_at;
    }

    public function setPaidAt(?\DateTimeInterface $paid_at): self
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getQrLink(): ?string
    {
        return $this->qr_link;
    }

    public function setQrLink(?string $qr_link): self
    {
        $this->qr_link = $qr_link;

        return $this;
    }

    public function getQrString(): ?string
    {
        return $this->qr_string;
    }

    public function setQrString(?string $qr_string): self
    {
        $this->qr_string = $qr_string;

        return $this;
    }
}
