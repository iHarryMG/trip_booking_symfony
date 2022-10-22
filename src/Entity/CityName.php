<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityNameRepository")
 */
class CityName
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $country_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $city_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $city_img;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryId(): ?int
    {
        return $this->country_id;
    }

    public function setCountryId(?int $country_id): self
    {
        $this->country_id = $country_id;

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->city_name;
    }

    public function setCityName(?string $city_name): self
    {
        $this->city_name = $city_name;

        return $this;
    }

    public function getCityImg(): ?string
    {
        return $this->city_img;
    }

    public function setCityImg(?string $city_img): self
    {
        $this->city_img = $city_img;

        return $this;
    }
}
