<?php

namespace App\Entity;

use App\Repository\HeroeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroeRepository::class)]
class Heroe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $healthPoints = null;

    #[ORM\Column]
    private ?int $attackPower = null;

    #[ORM\Column]
    private ?int $criticalStrikeChance = null;

    #[ORM\Column]
    private ?int $defense = null;

    #[ORM\Column]
    private ?float $experience = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $state = null;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\ManyToMany(targetEntity: Stage::class, mappedBy: 'heroes')]
    private Collection $stages;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'heroes')]
    private Collection $weapon_1;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class)]
    private Collection $weapon_2;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class)]
    private Collection $trinket;

    public function __construct()
    {
        $this->stages = new ArrayCollection();
        $this->weapon_1 = new ArrayCollection();
        $this->weapon_2 = new ArrayCollection();
        $this->trinket = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHealthPoints(): ?int
    {
        return $this->healthPoints;
    }

    public function setHealthPoints(int $healthPoints): static
    {
        $this->healthPoints = $healthPoints;

        return $this;
    }

    public function getAttackPower(): ?int
    {
        return $this->attackPower;
    }

    public function setAttackPower(int $attackPower): static
    {
        $this->attackPower = $attackPower;

        return $this;
    }

    public function getCriticalStrikeChance(): ?int
    {
        return $this->criticalStrikeChance;
    }

    public function setCriticalStrikeChance(int $criticalStrikeChance): static
    {
        $this->criticalStrikeChance = $criticalStrikeChance;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): static
    {
        $this->defense = $defense;

        return $this;
    }

    public function getExperience(): ?float
    {
        return $this->experience;
    }

    public function setExperience(float $experience): static
    {
        $this->experience = $experience;

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

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): static
    {
        if (!$this->stages->contains($stage)) {
            $this->stages->add($stage);
            $stage->addHero($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): static
    {
        if ($this->stages->removeElement($stage)) {
            $stage->removeHero($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getWeapon1(): Collection
    {
        return $this->weapon_1;
    }

    public function addWeapon1(Item $weapon1): static
    {
        if (!$this->weapon_1->contains($weapon1)) {
            $this->weapon_1->add($weapon1);
        }

        return $this;
    }

    public function removeWeapon1(Item $weapon1): static
    {
        $this->weapon_1->removeElement($weapon1);

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getWeapon2(): Collection
    {
        return $this->weapon_2;
    }

    public function addWeapon2(Item $weapon2): static
    {
        if (!$this->weapon_2->contains($weapon2)) {
            $this->weapon_2->add($weapon2);
        }

        return $this;
    }

    public function removeWeapon2(Item $weapon2): static
    {
        $this->weapon_2->removeElement($weapon2);

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getTrinket(): Collection
    {
        return $this->trinket;
    }

    public function addTrinket(Item $trinket): static
    {
        if (!$this->trinket->contains($trinket)) {
            $this->trinket->add($trinket);
        }

        return $this;
    }

    public function removeTrinket(Item $trinket): static
    {
        $this->trinket->removeElement($trinket);

        return $this;
    }
}
