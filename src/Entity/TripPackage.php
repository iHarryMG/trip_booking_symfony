<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TripPackageRepository")
 */
class TripPackage
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
    private $flight_info_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hotel_list_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $insurance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $welcome_service;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_special;

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
    private $is_active;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_removed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlightInfoId(): ?int
    {
        return $this->flight_info_id;
    }

    public function setFlightInfoId(?int $flight_info_id): self
    {
        $this->flight_info_id = $flight_info_id;

        return $this;
    }

    public function getHotelListId(): ?int
    {
        return $this->hotel_list_id;
    }

    public function setHotelListId(?int $hotel_list_id): self
    {
        $this->hotel_list_id = $hotel_list_id;

        return $this;
    }

    public function getMeal(): ?string
    {
        return $this->meal;
    }

    public function setMeal(?string $meal): self
    {
        $this->meal = $meal;

        return $this;
    }

    public function getInsurance(): ?string
    {
        return $this->insurance;
    }

    public function setInsurance(?string $insurance): self
    {
        $this->insurance = $insurance;

        return $this;
    }

    public function getWelcomeService(): ?string
    {
        return $this->welcome_service;
    }

    public function setWelcomeService(?string $welcome_service): self
    {
        $this->welcome_service = $welcome_service;

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

    public function getIsActive(): ?int
    {
        return $this->is_active;
    }

    public function setIsActive(?int $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getIsRemoved(): ?int
    {
        return $this->is_removed;
    }

    public function setIsRemoved(?int $is_removed): self
    {
        $this->is_removed = $is_removed;

        return $this;
    }
}
