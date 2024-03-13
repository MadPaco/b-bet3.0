<?php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OddRepository::class)]
class Odd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $gameId;

    #[ORM\Column(type: 'integer')]
    private $homeOdd;

    #[ORM\Column(type: 'integer')]
    private $awayOdd;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 1)]
    private $overUnder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameId(): ?int
    {
        return $this->gameId;
    }

    public function setGameId(int $gameId): self
    {
        $this->gameId = $gameId;
        return $this;
    }

    public function getHomeOdd(): ?int
    {
        return $this->homeOdd;
    }

    public function setHomeOdd(int $homeOdd): self
    {
        $this->homeOdd = $homeOdd;
        return $this;
    }

    public function getAwayOdd(): ?int
    {
        return $this->awayOdd;
    }

    public function setAwayOdd(int $awayOdd): self
    {
        $this->awayOdd = $awayOdd;
        return $this;
    }

    public function getOverUnder(): ?float
    {
        return $this->overUnder;
    }

    public function setOverUnder(float $overUnder): self
    {
        $this->overUnder = $overUnder;
        return $this;
    }
}