<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Game;
use App\Entity\User;

#[ORM\Entity(repositoryClass: "App\Repository\BetRepository")]

class Bet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'bets')]
    #[ORM\JoinColumn(nullable: false)]
    private $game;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "bets")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $homePrediction;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $awayPrediction;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $points;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastEdit;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $editCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getHomePrediction(): ?int
    {
        return $this->homePrediction;
    }

    public function setHomePrediction(?int $homePrediction): self
    {
        $this->homePrediction = $homePrediction;
        return $this;
    }

    public function getAwayPrediction(): ?int
    {
        return $this->awayPrediction;
    }

    public function setAwayPrediction(?int $awayPrediction): self
    {
        $this->awayPrediction = $awayPrediction;
        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;
        return $this;
    }

    public function getLastEdit(): ?\DateTimeInterface
    {
        return $this->lastEdit;
    }

    public function setLastEdit(\DateTimeInterface $lastEdit): self
    {
        $this->lastEdit = $lastEdit;
        return $this;
    }

    public function getEditCount(): ?int
    {
        return $this->editCount;
    }

    public function setEditCount(int $editCount): self
    {
        $this->editCount = $editCount;
        return $this;
    }
}
