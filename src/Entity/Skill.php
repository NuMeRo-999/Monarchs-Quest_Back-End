<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $attackDamage = null;

    /**
     * @var Collection<int, Effect>
     */
    #[ORM\ManyToMany(targetEntity: Effect::class, inversedBy: 'skills')]
    private Collection $effect;

    /**
     * @var Collection<int, Heroe>
     */
    #[ORM\ManyToMany(targetEntity: Heroe::class, mappedBy: 'abilities')]
    private Collection $heroes;

    #[ORM\Column(type: 'string')]
    private string $imageFilename;

    public function __construct()
    {
        $this->effect = new ArrayCollection();
        $this->heroes = new ArrayCollection();
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

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAttackDamage(): ?int
    {
        return $this->attackDamage;
    }

    public function setAttackDamage(int $attackDamage): static
    {
        $this->attackDamage = $attackDamage;

        return $this;
    }

    /**
     * @return Collection<int, Effect>
     */
    public function getEffect(): Collection
    {
        return $this->effect;
    }

    public function addEffect(Effect $effect): static
    {
        if (!$this->effect->contains($effect)) {
            $this->effect->add($effect);
        }

        return $this;
    }

    public function removeEffect(Effect $effect): static
    {
        $this->effect->removeElement($effect);

        return $this;
    }

    /**
     * @return Collection<int, Heroe>
     */
    public function getHeroes(): Collection
    {
        return $this->heroes;
    }

    public function addHero(Heroe $hero): static
    {
        if (!$this->heroes->contains($hero)) {
            $this->heroes->add($hero);
            $hero->addAbility($this);
        }

        return $this;
    }

    public function removeHero(Heroe $hero): static
    {
        if ($this->heroes->removeElement($hero)) {
            $hero->removeAbility($this);
        }

        return $this;
    }

    public function getImageFilename(): string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(string $ImageFilename): self
    {
        $this->imageFilename = $ImageFilename;

        return $this;
    }
}
