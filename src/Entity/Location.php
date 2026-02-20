<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120, unique: true)]
    private ?string $name = null;

    #[ORM\Column]
    private int $defence = 0;

    #[ORM\Column]
    private int $food = 0;

    #[ORM\Column]
    private int $morale = 0;

    #[ORM\Column]
    private int $standing = 0;

    /**
     * @var Collection<int, Npc>
     */
    #[ORM\OneToMany(targetEntity: Npc::class, mappedBy: 'location')]
    private Collection $npcs;

    public function __construct()
    {
        $this->npcs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDefence(): int
    {
        return $this->defence;
    }

    public function setDefence(int $defence): static
    {
        $this->defence = $defence;

        return $this;
    }

    public function getFood(): int
    {
        return $this->food;
    }

    public function setFood(int $food): static
    {
        $this->food = $food;

        return $this;
    }

    public function getMorale(): int
    {
        return $this->morale;
    }

    public function setMorale(int $morale): static
    {
        $this->morale = $morale;

        return $this;
    }

    public function getStanding(): int
    {
        return $this->standing;
    }

    public function setStanding(int $standing): static
    {
        $this->standing = $standing;

        return $this;
    }

    /**
     * @return Collection<int, Npc>
     */
    public function getNpcs(): Collection
    {
        return $this->npcs;
    }

    public function addNpc(Npc $npc): static
    {
        if (!$this->npcs->contains($npc)) {
            $this->npcs->add($npc);
            $npc->setLocation($this);
        }

        return $this;
    }

    public function removeNpc(Npc $npc): static
    {
        if ($this->npcs->removeElement($npc)) {
            if ($npc->getLocation() === $this) {
                $npc->setLocation(null);
            }
        }

        return $this;
    }
}
