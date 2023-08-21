<?php

namespace App\Entity;

use App\Repository\LockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LockRepository::class)]
class Lock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $device_id = null;

    #[ORM\ManyToOne(inversedBy: 'locks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?locktype $lock_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeviceId(): ?string
    {
        return $this->device_id;
    }

    public function setDeviceId(string $device_id): static
    {
        $this->device_id = $device_id;

        return $this;
    }

    public function getLockType(): ?locktype
    {
        return $this->lock_type;
    }

    public function setLockType(?locktype $lock_type): static
    {
        $this->lock_type = $lock_type;

        return $this;
    }
}
