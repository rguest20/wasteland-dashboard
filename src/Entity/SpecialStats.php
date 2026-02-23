<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class SpecialStats
{
    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 4, max: 12)]
    private int $strength = 5;

    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 4, max: 12)]
    private int $perception = 5;

    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 4, max: 12)]
    private int $endurance = 5;

    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 4, max: 12)]
    private int $charisma = 5;

    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 4, max: 12)]
    private int $intelligence = 5;

    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 4, max: 12)]
    private int $agility = 5;

    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 4, max: 12)]
    private int $luck = 5;

    public function getStrength(): int
    {
        return $this->strength;
    }

    public function setStrength(int $strength): self
    {
        $this->strength = $strength;

        return $this;
    }

    public function getPerception(): int
    {
        return $this->perception;
    }

    public function setPerception(int $perception): self
    {
        $this->perception = $perception;

        return $this;
    }

    public function getEndurance(): int
    {
        return $this->endurance;
    }

    public function setEndurance(int $endurance): self
    {
        $this->endurance = $endurance;

        return $this;
    }

    public function getCharisma(): int
    {
        return $this->charisma;
    }

    public function setCharisma(int $charisma): self
    {
        $this->charisma = $charisma;

        return $this;
    }

    public function getIntelligence(): int
    {
        return $this->intelligence;
    }

    public function setIntelligence(int $intelligence): self
    {
        $this->intelligence = $intelligence;

        return $this;
    }

    public function getAgility(): int
    {
        return $this->agility;
    }

    public function setAgility(int $agility): self
    {
        $this->agility = $agility;

        return $this;
    }

    public function getLuck(): int
    {
        return $this->luck;
    }

    public function setLuck(int $luck): self
    {
        $this->luck = $luck;

        return $this;
    }
}
