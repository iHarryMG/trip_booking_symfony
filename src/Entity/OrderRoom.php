<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRoomRepository")
 */
class OrderRoom
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
    private $order_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $room_price_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $room_name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $room_price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $children_price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total_price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $paid_price;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $order_status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $room_price_bb;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $adult_price;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $room_qty;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoomPriceId(): ?int
    {
        return $this->room_price_id;
    }

    public function setRoomPriceId(?int $room_price_id): self
    {
        $this->room_price_id = $room_price_id;

        return $this;
    }

    public function getRoomName(): ?string
    {
        return $this->room_name;
    }

    public function setRoomName(?string $room_name): self
    {
        $this->room_name = $room_name;

        return $this;
    }

    public function getRoomPrice(): ?float
    {
        return $this->room_price;
    }

    public function setRoomPrice(?float $room_price): self
    {
        $this->room_price = $room_price;

        return $this;
    }

    public function getChildrenPrice(): ?float
    {
        return $this->children_price;
    }

    public function setChildrenPrice(?float $children_price): self
    {
        $this->children_price = $children_price;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(?float $total_price): self
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getPaidPrice(): ?float
    {
        return $this->paid_price;
    }

    public function setPaidPrice(?float $paid_price): self
    {
        $this->paid_price = $paid_price;

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

    public function getRoomPriceBb(): ?float
    {
        return $this->room_price_bb;
    }

    public function setRoomPriceBb(?float $room_price_bb): self
    {
        $this->room_price_bb = $room_price_bb;

        return $this;
    }

    public function getAdultPrice(): ?float
    {
        return $this->adult_price;
    }

    public function setAdultPrice(?float $adult_price): self
    {
        $this->adult_price = $adult_price;

        return $this;
    }

    public function getRoomQty(): ?int
    {
        return $this->room_qty;
    }

    public function setRoomQty(?int $room_qty): self
    {
        $this->room_qty = $room_qty;

        return $this;
    }
}
