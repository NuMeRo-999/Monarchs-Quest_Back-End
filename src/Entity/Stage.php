<?php

namespace App\Entity;

use App\Repository\StageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageRepository::class)]
class Stage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $stage = null;

    /**
     * @var Collection<int, Heroe>
     */
    #[ORM\ManyToMany(targetEntity: Heroe::class, inversedBy: 'stages')]
    private Collection $heroes;

    #[ORM\ManyToOne(inversedBy: 'stage')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SaveSlot $saveSlot = null;

    /**
     * @var Collection<int, Enemy>
     */
    #[ORM\OneToMany(targetEntity: Enemy::class, mappedBy: 'stage')]
    private Collection $enemies;

    public function __construct()
    {
        $this->heroes = new ArrayCollection();
        $this->enemies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStage(): ?int
    {
        return $this->stage;
    }

    public function setStage(int $stage): static
    {
        $this->stage = $stage;

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
        }

        return $this;
    }

    public function removeHero(Heroe $hero): static
    {
        $this->heroes->removeElement($hero);

        return $this;
    }

    public function getSaveSlot(): ?SaveSlot
    {
        return $this->saveSlot;
    }

    public function setSaveSlot(?SaveSlot $saveSlot): static
    {
        $this->saveSlot = $saveSlot;

        return $this;
    }

    /**
     * @return Collection<int, Enemy>
     */
    public function getEnemies(): Collection
    {
        return $this->enemies;
    }

    public function addEnemy(Enemy $enemy): static
    {
        if (!$this->enemies->contains($enemy)) {
            $this->enemies->add($enemy);
            $enemy->setStage($this);
        }

        return $this;
    }

    public function removeEnemy(Enemy $enemy): static
    {
        if ($this->enemies->removeElement($enemy)) {
            // set the owning side to null (unless already changed)
            if ($enemy->getStage() === $this) {
                $enemy->setStage(null);
            }
        }

        return $this;
    }
}
