<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlightInfoRepository")
 */
class FlightInfo
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
    private $country_id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $city_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $departure_datetime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $arrival_datetime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direction;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $night_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $night_count_plus;

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

    public function getCountryId(): ?int
    {
        return $this->country_id;
    }

    public function setCountryId(?int $country_id): self
    {
        $this->country_id = $country_id;

        return $this;
    }

    public function getCityId(): ?int
    {
        return $this->city_id;
    }

    public function setCityId(?int $city_id): self
    {
        $this->city_id = $city_id;

        return $this;
    }

    public function getDepartureDatetime(): ?\DateTimeInterface
    {
        return $this->departure_datetime;
    }

    public function setDepartureDatetime(?\DateTimeInterface $departure_datetime): self
    {
        $this->departure_datetime = $departure_datetime;

        return $this;
    }

    public function getArrivalDatetime(): ?\DateTimeInterface
    {
        return $this->arrival_datetime;
    }

    public function setArrivalDatetime(?\DateTimeInterface $arrival_datetime): self
    {
        $this->arrival_datetime = $arrival_datetime;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getNightCount(): ?int
    {
        return $this->night_count;
    }

    public function setNightCount(?int $night_count): self
    {
        $this->night_count = $night_count;

        return $this;
    }

    public function getNightCountPlus(): ?int
    {
        return $this->night_count_plus;
    }

    public function setNightCountPlus(?int $night_count_plus): self
    {
        $this->night_count_plus = $night_count_plus;

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
