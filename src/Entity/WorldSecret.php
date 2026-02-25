<?php

namespace App\Entity;

use App\Repository\WorldSecretRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorldSecretRepository::class)]
class WorldSecret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category = null;

    /**
     * @var Collection<int, Knowledge>
     */
    #[ORM\OneToMany(targetEntity: Knowledge::class, mappedBy: 'world_secret')]
    private Collection $knowledge;

    public function __construct()
    {
        $this->knowledge = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Knowledge>
     */
    public function getKnowledge(): Collection
    {
        return $this->knowledge;
    }

    public function addKnowledge(Knowledge $knowledge): static
    {
        if (!$this->knowledge->contains($knowledge)) {
            $this->knowledge->add($knowledge);
            $knowledge->setWorldSecret($this);
        }

        return $this;
    }

    public function removeKnowledge(Knowledge $knowledge): static
    {
        if ($this->knowledge->removeElement($knowledge)) {
            // set the owning side to null (unless already changed)
            if ($knowledge->getWorldSecret() === $this) {
                $knowledge->setWorldSecret(null);
            }
        }

        return $this;
    }
}
