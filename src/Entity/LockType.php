<?php

namespace App\Entity;

use App\Repository\LockTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LockTypeRepository::class)]
class LockType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'lock_type', targetEntity: Lock::class, orphanRemoval: true)]
    private Collection $locks;

    #[ORM\Column]
    private ?float $battery_voltage_min = null;

    #[ORM\Column]
    private ?float $battery_voltage_max = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $cellular_signal_quality_min = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $cellular_signal_quality_max = null;

    public function __construct()
    {
        $this->locks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Lock>
     */
    public function getLocks(): Collection
    {
        return $this->locks;
    }

    public function addLock(Lock $lock): static
    {
        if (!$this->locks->contains($lock)) {
            $this->locks->add($lock);
            $lock->setLockType($this);
        }

        return $this;
    }

    public function removeLock(Lock $lock): static
    {
        if ($this->locks->removeElement($lock)) {
            // set the owning side to null (unless already changed)
            if ($lock->getLockType() === $this) {
                $lock->setLockType(null);
            }
        }

        return $this;
    }

    public function getBatteryVoltageMin(): ?float
    {
        return $this->battery_voltage_min;
    }

    public function setBatteryVoltageMin(float $battery_voltage_min): static
    {
        $this->battery_voltage_min = $battery_voltage_min;

        return $this;
    }

    public function getBatteryVoltageMax(): ?float
    {
        return $this->battery_voltage_max;
    }

    public function setBatteryVoltageMax(float $battery_voltage_max): static
    {
        $this->battery_voltage_max = $battery_voltage_max;

        return $this;
    }

    public function getCellularSignalQualityMin(): ?int
    {
        return $this->cellular_signal_quality_min;
    }

    public function setCellularSignalQualityMin(?int $cellular_signal_quality_min): static
    {
        $this->cellular_signal_quality_min = $cellular_signal_quality_min;

        return $this;
    }

    public function getCellularSignalQualityMax(): ?int
    {
        return $this->cellular_signal_quality_max;
    }

    public function setCellularSignalQualityMax(?int $cellular_signal_quality_max): static
    {
        $this->cellular_signal_quality_max = $cellular_signal_quality_max;

        return $this;
    }
}
