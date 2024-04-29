<?php

namespace App\Entity;

use App\Repository\HeroeRepository;
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
}
