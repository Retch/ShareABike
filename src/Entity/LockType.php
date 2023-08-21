<?php

namespace App\Entity;

use App\Repository\LockTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
}
