<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $criticalStrikeChance = null;

    #[ORM\Column]
    private ?int $attackPower = null;

    #[ORM\Column]
    private ?int $defense = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $rarity = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * @var Collection<int, SaveSlot>
     */
    #[ORM\ManyToMany(targetEntity: SaveSlot::class, mappedBy: 'inventario')]
    private Collection $saveSlots;

    /**
     * @var Collection<int, Heroe>
     */
    #[ORM\ManyToMany(targetEntity: Heroe::class, mappedBy: 'weapon_1')]
    private Collection $heroes;

    #[ORM\Column(type: 'string')]
    private string $imageFilename;

    #[ORM\Column]
    private ?bool $state = null;

    #[ORM\Column]
    private ?int $healthPoints = null;

    #[ORM\Column]
    private ?int $maxHealthPoints = null;

    public function __construct()
    {
        $this->saveSlots = new ArrayCollection();
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

    public function getCriticalStrikeChance(): ?int
    {
        return $this->criticalStrikeChance;
    }

    public function setCriticalStrikeChance(int $criticalStrikeChance): static
    {
        $this->criticalStrikeChance = $criticalStrikeChance;

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

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): static
    {
        $this->defense = $defense;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getRarity(): ?string
    {
        return $this->rarity;
    }

    public function setRarity(string $rarity): static
    {
        $this->rarity = $rarity;

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

    /**
     * @return Collection<int, SaveSlot>
     */
    public function getSaveSlots(): Collection
    {
        return $this->saveSlots;
    }

    public function addSaveSlot(SaveSlot $saveSlot): static
    {
        if (!$this->saveSlots->contains($saveSlot)) {
            $this->saveSlots->add($saveSlot);
            $saveSlot->addInventario($this);
        }

        return $this;
    }

    public function removeSaveSlot(SaveSlot $saveSlot): static
    {
        if ($this->saveSlots->removeElement($saveSlot)) {
            $saveSlot->removeInventario($this);
        }

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
            $hero->addWeapon1($this);
        }

        return $this;
    }

    public function removeHero(Heroe $hero): static
    {
        if ($this->heroes->removeElement($hero)) {
            $hero->removeWeapon1($this);
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

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): static
    {
        $this->state = $state;

        return $this;
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

    public function getMaxHealthPoints(): ?int
    {
        return $this->maxHealthPoints;
    }

    public function setMaxHealthPoints(int $maxHealthPoints): static
    {
        $this->maxHealthPoints = $maxHealthPoints;

        return $this;
    }
}
