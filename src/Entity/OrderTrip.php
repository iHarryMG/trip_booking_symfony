<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderTripRepository")
 */
class OrderTrip
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $invoice_num;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_tc_confirmed;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $adult_count;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $children_count;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $children_age;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trip_id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total_amount;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $order_status;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ip_address;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $car_price_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceNum(): ?string
    {
        return $this->invoice_num;
    }

    public function setInvoiceNum(?string $invoice_num): self
    {
        $this->invoice_num = $invoice_num;

        return $this;
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

    public function getIsTcConfirmed(): ?int
    {
        return $this->is_tc_confirmed;
    }

    public function setIsTcConfirmed(?int $is_tc_confirmed): self
    {
        $this->is_tc_confirmed = $is_tc_confirmed;

        return $this;
    }

    public function getAdultCount(): ?int
    {
        return $this->adult_count;
    }

    public function setAdultCount(?int $adult_count): self
    {
        $this->adult_count = $adult_count;

        return $this;
    }

    public function getChildrenCount(): ?int
    {
        return $this->children_count;
    }

    public function setChildrenCount(?int $children_count): self
    {
        $this->children_count = $children_count;

        return $this;
    }

    public function getChildrenAge(): ?string
    {
        return $this->children_age;
    }

    public function setChildrenAge(?string $children_age): self
    {
        $this->children_age = $children_age;

        return $this;
    }

    public function getTripId(): ?int
    {
        return $this->trip_id;
    }

    public function setTripId(?int $trip_id): self
    {
        $this->trip_id = $trip_id;

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

    public function getOrderStatus(): ?string
    {
        return $this->order_status;
    }

    public function setOrderStatus(?string $order_status): self
    {
        $this->order_status = $order_status;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(?string $ip_address): self
    {
        $this->ip_address = $ip_address;

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

    public function getCarPriceId(): ?int
    {
        return $this->car_price_id;
    }

    public function setCarPriceId(?int $car_price_id): self
    {
        $this->car_price_id = $car_price_id;

        return $this;
    }
}
