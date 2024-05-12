<?php

namespace App\Entity;

use App\Repository\SaveSlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaveSlotRepository::class)]
class SaveSlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private ?int $money = null;

    /**
     * @Groups({"saveSlot_serialization"})
     */
    #[ORM\ManyToOne(inversedBy: 'saveSlot')]
    private ?Game $game = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'saveSlots')]
    private Collection $inventario;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\OneToMany(targetEntity: Stage::class, mappedBy: 'saveSlot')]
    private Collection $stage;

    #[ORM\Column]
    private ?int $kills = null;

    #[ORM\Column]
    private ?int $state = null;

    public function __construct()
    {
        $this->inventario = new ArrayCollection();
        $this->stage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getMoney(): ?int
    {
        return $this->money;
    }

    public function setMoney(int $money): static
    {
        $this->money = $money;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getInventario(): Collection
    {
        return $this->inventario;
    }

    public function addInventario(Item $inventario): static
    {
        if (!$this->inventario->contains($inventario)) {
            $this->inventario->add($inventario);
        }

        return $this;
    }

    public function removeInventario(Item $inventario): static
    {
        $this->inventario->removeElement($inventario);

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStage(): Collection
    {
        return $this->stage;
    }

    public function addStage(Stage $stage): static
    {
        if (!$this->stage->contains($stage)) {
            $this->stage->add($stage);
            $stage->setSaveSlot($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): static
    {
        if ($this->stage->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getSaveSlot() === $this) {
                $stage->setSaveSlot(null);
            }
        }

        return $this;
    }

    public function getKills(): ?int
    {
        return $this->kills;
    }

    public function setKills(int $kills): static
    {
        $this->kills = $kills;

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
}
