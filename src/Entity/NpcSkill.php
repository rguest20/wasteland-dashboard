<?php

namespace App\Entity;

use App\Repository\NpcSkillRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NpcSkillRepository::class)]
class NpcSkill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'npcSkills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Npc $npc_id = null;

    #[ORM\ManyToOne(inversedBy: 'npcSkills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Skill $skill_id = null;

    #[ORM\Column]
    private ?int $level = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNpcId(): ?Npc
    {
        return $this->npc_id;
    }

    public function setNpcId(?Npc $npc_id): static
    {
        $this->npc_id = $npc_id;

        return $this;
    }

    public function getSkillId(): ?Skill
    {
        return $this->skill_id;
    }

    public function setSkillId(?Skill $skill_id): static
    {
        $this->skill_id = $skill_id;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }
}
