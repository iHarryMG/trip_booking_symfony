<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HotelPhotoRepository")
 */
class HotelPhoto
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
    private $hotel_list_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hotel_img;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_special;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_active;

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

    public function getHotelListId(): ?int
    {
        return $this->hotel_list_id;
    }

    public function setHotelListId(?int $hotel_list_id): self
    {
        $this->hotel_list_id = $hotel_list_id;

        return $this;
    }

    public function getHotelImg(): ?string
    {
        return $this->hotel_img;
    }

    public function setHotelImg(?string $hotel_img): self
    {
        $this->hotel_img = $hotel_img;

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

    public function getIsActive(): ?int
    {
        return $this->is_active;
    }

    public function setIsActive(?int $is_active): self
    {
        $this->is_active = $is_active;

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
