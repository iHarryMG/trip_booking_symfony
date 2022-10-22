<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomPriceRepository")
 */
class RoomPrice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $trip_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $room_name_id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_a;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_bb;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_discounted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_special;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_discounted_bb;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoomNameId(): ?int
    {
        return $this->room_name_id;
    }

    public function setRoomNameId(?int $room_name_id): self
    {
        $this->room_name_id = $room_name_id;

        return $this;
    }

    public function getPriceA(): ?float
    {
        return $this->price_a;
    }

    public function setPriceA(?float $price_a): self
    {
        $this->price_a = $price_a;

        return $this;
    }

    public function getPriceBb(): ?float
    {
        return $this->price_bb;
    }

    public function setPriceBb(?float $price_bb): self
    {
        $this->price_bb = $price_bb;

        return $this;
    }

    public function getPriceDiscounted(): ?float
    {
        return $this->price_discounted;
    }

    public function setPriceDiscounted(?float $price_discounted): self
    {
        $this->price_discounted = $price_discounted;

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

    public function getIsSpecial(): ?int
    {
        return $this->is_special;
    }

    public function setIsSpecial(?int $is_special): self
    {
        $this->is_special = $is_special;

        return $this;
    }

    public function getPriceDiscountedBb(): ?float
    {
        return $this->price_discounted_bb;
    }

    public function setPriceDiscountedBb(?float $price_discounted_bb): self
    {
        $this->price_discounted_bb = $price_discounted_bb;

        return $this;
    }
}
