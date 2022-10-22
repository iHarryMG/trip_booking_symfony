<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarPriceRepository")
 */
class CarPrice
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
    private $car_info_id;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $way;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $adult_price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $child_price;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarInfoId(): ?int
    {
        return $this->car_info_id;
    }

    public function setCarInfoId(?int $car_info_id): self
    {
        $this->car_info_id = $car_info_id;

        return $this;
    }

    public function getWay(): ?int
    {
        return $this->way;
    }

    public function setWay(?int $way): self
    {
        $this->way = $way;

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

    public function getChildPrice(): ?float
    {
        return $this->child_price;
    }

    public function setChildPrice(?float $child_price): self
    {
        $this->child_price = $child_price;

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
}
