<?php

namespace App\Entity;

use App\Repository\LockRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $battery_percentage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_contact = null;

    #[ORM\OneToOne(mappedBy: 'lock', cascade: ['persist', 'remove'])]
    private ?Bike $bike = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_locked = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_connected_to_adapter = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_position_time = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $cellular_signal_quality_percentage = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $satellites = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude_degrees = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude_degrees = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $latitude_hemisphere = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $longitude_hemisphere = null;

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

    public function getBatteryPercentage(): ?int
    {
        return $this->battery_percentage;
    }

    public function setBatteryPercentage(?int $battery_percentage): static
    {
        $this->battery_percentage = $battery_percentage;

        return $this;
    }

    public function getLastContact(): ?\DateTimeInterface
    {
        return $this->last_contact;
    }

    public function setLastContact(?\DateTimeInterface $last_contact): static
    {
        $this->last_contact = $last_contact;

        return $this;
    }

    public function getBike(): ?Bike
    {
        return $this->bike;
    }

    public function setBike(?Bike $bike): static
    {
        // unset the owning side of the relation if necessary
        if ($bike === null && $this->bike !== null) {
            $this->bike->setLock(null);
        }

        // set the owning side of the relation if necessary
        if ($bike !== null && $bike->getLock() !== $this) {
            $bike->setLock($this);
        }

        $this->bike = $bike;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->is_locked;
    }

    public function setIsLocked(?bool $is_locked): static
    {
        $this->is_locked = $is_locked;

        return $this;
    }

    public function isConnectedToAdapter(): ?bool
    {
        return $this->is_connected_to_adapter;
    }

    public function setIsConnectedToAdapter(?bool $is_connected_to_adapter): static
    {
        $this->is_connected_to_adapter = $is_connected_to_adapter;

        return $this;
    }

    public function getLastPositionTime(): ?\DateTimeInterface
    {
        return $this->last_position_time;
    }

    public function setLastPositionTime(?\DateTimeInterface $last_position_time): static
    {
        $this->last_position_time = $last_position_time;

        return $this;
    }

    public function getCellularSignalQualityPercentage(): ?int
    {
        return $this->cellular_signal_quality_percentage;
    }

    public function setCellularSignalQualityPercentage(?int $cellular_signal_quality_percentage): static
    {
        $this->cellular_signal_quality_percentage = $cellular_signal_quality_percentage;

        return $this;
    }

    public function getSatellites(): ?int
    {
        return $this->satellites;
    }

    public function setSatellites(?int $satellites): static
    {
        $this->satellites = $satellites;

        return $this;
    }

    public function getLatitudeDegrees(): ?float
    {
        return $this->latitude_degrees;
    }

    public function setLatitudeDegrees(?float $latitude_degrees): static
    {
        $this->latitude_degrees = $latitude_degrees;

        return $this;
    }

    public function getLongitudeDegrees(): ?float
    {
        return $this->longitude_degrees;
    }

    public function setLongitudeDegrees(?float $longitude_degrees): static
    {
        $this->longitude_degrees = $longitude_degrees;

        return $this;
    }

    public function getLatitudeHemisphere(): ?string
    {
        return $this->latitude_hemisphere;
    }

    public function setLatitudeHemisphere(?string $latitude_hemisphere): static
    {
        $this->latitude_hemisphere = $latitude_hemisphere;

        return $this;
    }

    public function getLongitudeHemisphere(): ?string
    {
        return $this->longitude_hemisphere;
    }

    public function setLongitudeHemisphere(?string $longitude_hemisphere): static
    {
        $this->longitude_hemisphere = $longitude_hemisphere;

        return $this;
    }
}
