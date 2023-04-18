<?php

namespace App\Entity;

use App\Repository\FruitsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FruitsRepository::class)]
class Fruits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $fruit_id = null;

    #[ORM\Column(length: 255)]
    private ?string $family = null;

    #[ORM\Column(length: 255)]
    private ?string $fruit_order = null;

    #[ORM\Column]
    private ?bool $favorite_status = null;

    #[ORM\Column(length: 255)]
    private ?string $genus = null;

    #[ORM\Column(length: 255)]
    private ?string $nutritions = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFruitId(): ?string
    {
        return $this->fruit_id;
    }

    public function setFruitId(string $fruit_id): self
    {
        $this->fruit_id = $fruit_id;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getFruitOrder(): ?string
    {
        return $this->fruit_order;
    }

    public function setFruitOrder(string $fruit_order): self
    {
        $this->fruit_order = $fruit_order;

        return $this;
    }
    
    public function isFavoriteStatus(): ?bool
    {
        return $this->favorite_status;
    }

    public function setFavoriteStatus($favorite_status): self
    {
        $this->favorite_status = (bool)$favorite_status;

        return $this;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(string $genus): self
    {
        $this->genus = $genus;

        return $this;
    }

    public function getNutritions(): ?string
    {
        return $this->nutritions;
    }

    public function setNutritions(string $nutritions): self
    {
        $this->nutritions = $nutritions;

        return $this;
    }
}
