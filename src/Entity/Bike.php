<?php

namespace App\Entity;

use App\Repository\BikeRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Lock;

#[ORM\Entity(repositoryClass: BikeRepository::class)]
class Bike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $is_available = null;

    #[ORM\OneToOne(inversedBy: 'bike', cascade: ['persist', 'remove'])]
    private ?lock $lock = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAvailable(): ?bool
    {
        return $this->is_available;
    }

    public function setIsAvailable(bool $is_available): static
    {
        $this->is_available = $is_available;

        return $this;
    }

    public function getLock(): ?lock
    {
        return $this->lock;
    }

    public function setLock(?lock $lock): static
    {
        $this->lock = $lock;

        return $this;
    }
}
