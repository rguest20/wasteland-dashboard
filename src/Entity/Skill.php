<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, NpcSkill>
     */
    #[ORM\OneToMany(targetEntity: NpcSkill::class, mappedBy: 'skill_id', orphanRemoval: true)]
    private Collection $npcSkills;

    public function __construct()
    {
        $this->npcSkills = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, NpcSkill>
     */
    public function getNpcSkills(): Collection
    {
        return $this->npcSkills;
    }

    public function addNpcSkill(NpcSkill $npcSkill): static
    {
        if (!$this->npcSkills->contains($npcSkill)) {
            $this->npcSkills->add($npcSkill);
            $npcSkill->setSkillId($this);
        }

        return $this;
    }

    public function removeNpcSkill(NpcSkill $npcSkill): static
    {
        if ($this->npcSkills->removeElement($npcSkill)) {
            // set the owning side to null (unless already changed)
            if ($npcSkill->getSkillId() === $this) {
                $npcSkill->setSkillId(null);
            }
        }

        return $this;
    }
}
