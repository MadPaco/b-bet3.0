<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Game;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: "gameID", referencedColumnName: "id")]
    private $game;

    #[ORM\Column(type: 'integer')]
    private $homeScore;

    #[ORM\Column(type: 'integer')]
    private $awayScore;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }
    
    public function setGame(?Game $game): self
    {
        $this->game = $game;
    
        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(int $homeScore): self
    {
        $this->homeScore = $homeScore;
        return $this;
    }

    public function getAwayScore(): ?int
    {
        return $this->awayScore;
    }

    public function setAwayScore(int $awayScore): self
    {
        $this->awayScore = $awayScore;
        return $this;
    }
}