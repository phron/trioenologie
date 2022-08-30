<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Profile::class)]
    private Collection $profiles;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Profile>
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    public function addProfile(Profile $profile): self
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles->add($profile);
            $profile->setStatus($this);
        }

        return $this;
    }

    public function removeProfile(Profile $profile): self
    {
        if ($this->profiles->removeElement($profile)) {
            // set the owning side to null (unless already changed)
            if ($profile->getStatus() === $this) {
                $profile->setStatus(null);
            }
        }

        return $this;
    }
}
