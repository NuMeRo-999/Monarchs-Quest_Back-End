<?php

namespace App\Entity;

use App\Repository\EnemyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnemyRepository::class)]
class Enemy
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
    private ?int $defense = null;

    #[ORM\Column]
    private ?int $criticalStrikeChance = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $state = null;

    #[ORM\ManyToOne(inversedBy: 'enemies', cascade: ['remove'])]
    private ?Stage $stage = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string')]
    private string $imageFilename;

    #[ORM\Column]
    private ?int $maxHealthPoints = null;

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

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): static
    {
        $this->defense = $defense;

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

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function setStage(?Stage $stage): static
    {
        $this->stage = $stage;

        return $this;
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

    public function getImageFilename(): string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(string $ImageFilename): self
    {
        $this->imageFilename = $ImageFilename;

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
