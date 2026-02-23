<?php

namespace App\Entity;

use App\Repository\NpcRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NpcRepository::class)]
class Npc
{
    public function __construct()
    {
        $this->special = new SpecialStats();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'npcs')]
    private ?Role $role = null;

    #[ORM\ManyToOne(inversedBy: 'npcs')]
    private ?Location $location = null;

    #[ORM\Embedded(class: SpecialStats::class, columnPrefix: false)]
    #[Assert\Valid]
    private SpecialStats $special;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getSpecial(): SpecialStats
    {
        return $this->special;
    }

    public function setSpecial(SpecialStats $special): static
    {
        $this->special = $special;

        return $this;
    }

    public function getStrength(): int
    {
        return $this->special->getStrength();
    }

    public function setStrength(int $value): static
    {
        $this->special->setStrength($value);

        return $this;
    }

    public function getPerception(): int
    {
        return $this->special->getPerception();
    }

    public function setPerception(int $value): static
    {
        $this->special->setPerception($value);

        return $this;
    }

    public function getEndurance(): int
    {
        return $this->special->getEndurance();
    }

    public function setEndurance(int $value): static
    {
        $this->special->setEndurance($value);

        return $this;
    }

    public function getCharisma(): int
    {
        return $this->special->getCharisma();
    }

    public function setCharisma(int $value): static
    {
        $this->special->setCharisma($value);

        return $this;
    }

    public function getIntelligence(): int
    {
        return $this->special->getIntelligence();
    }

    public function setIntelligence(int $value): static
    {
        $this->special->setIntelligence($value);

        return $this;
    }

    public function getAgility(): int
    {
        return $this->special->getAgility();
    }

    public function setAgility(int $value): static
    {
        $this->special->setAgility($value);

        return $this;
    }

    public function getLuck(): int
    {
        return $this->special->getLuck();
    }

    public function setLuck(int $value): static
    {
        $this->special->setLuck($value);

        return $this;
    }
}
