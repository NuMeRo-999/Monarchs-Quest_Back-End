<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'game')]
    private Collection $user;

    /**
     * @var Collection<int, SaveSlot>
     */
    #[ORM\OneToMany(targetEntity: SaveSlot::class, mappedBy: 'game')]
    private Collection $saveSlot;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->saveSlot = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setGame($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGame() === $this) {
                $user->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SaveSlot>
     */
    public function getSaveSlot(): Collection
    {
        return $this->saveSlot;
    }

    public function addSaveSlot(SaveSlot $saveSlot): static
    {
        if (!$this->saveSlot->contains($saveSlot)) {
            $this->saveSlot->add($saveSlot);
            $saveSlot->setGame($this);
        }

        return $this;
    }

    public function removeSaveSlot(SaveSlot $saveSlot): static
    {
        if ($this->saveSlot->removeElement($saveSlot)) {
            // set the owning side to null (unless already changed)
            if ($saveSlot->getGame() === $this) {
                $saveSlot->setGame(null);
            }
        }

        return $this;
    }
}
