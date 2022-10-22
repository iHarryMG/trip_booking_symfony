<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryNameRepository")
 */
class CountryName
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $country_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $country_img;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    public $is_one_item;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryName(): ?string
    {
        return $this->country_name;
    }

    public function setCountryName(?string $country_name): self
    {
        $this->country_name = $country_name;

        return $this;
    }

    public function getCountryImg(): ?string
    {
        return $this->country_img;
    }

    public function setCountryImg(?string $country_img): self
    {
        $this->country_img = $country_img;

        return $this;
    }

    public function getIsOneItem(): ?int
    {
        return $this->is_one_item;
    }

    public function setIsOneItem(?int $is_one_item): self
    {
        $this->is_one_item = $is_one_item;

        return $this;
    }
}
