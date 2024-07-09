<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\NflTeam;
use App\Entity\Bet;
use App\Entity\Odd;
use App\Entity\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\GameRepository;


#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $weekNumber;

    #[ORM\Column(type: 'datetime')]
    private $date;

    #[ORM\Column(type: 'string', length: 255)]
    private $location;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(name: "homeTeam", referencedColumnName: "id")]
    private $homeTeam;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(name: "awayTeam", referencedColumnName: "id")]
    private $awayTeam;

    #[ORM\OneToMany(targetEntity: Bet::class, mappedBy: "game")]
    private $bets;

    #[ORM\Column(name: 'homeOdds', type: 'integer', nullable: true)]
    private $homeOdds;

    #[ORM\Column(name: 'awayOdds', type: 'integer', nullable: true)]
    private $awayOdds;

    #[ORM\Column(name: 'overUnder', type: 'decimal', precision: 5, scale: 1, nullable: true)]
    private $overUnder;

    #[ORM\Column(name: 'homeScore', type: 'integer', nullable: true)]
    private $homeScore;

    #[ORM\Column(name: 'awayScore', type: 'integer', nullable: true)]
    private $awayScore;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getWeekNumber(): ?int
    {
        return $this->weekNumber;
    }

    public function setWeekNumber(int $weekNumber): self
    {
        $this->weekNumber = $weekNumber;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getHomeTeam(): ?NflTeam
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(NflTeam $homeTeam): self
    {
        $this->homeTeam = $homeTeam;
        return $this;
    }

    public function getAwayTeam(): ?NflTeam
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(?NflTeam $awayTeam): self
    {
        $this->awayTeam = $awayTeam;
        return $this;
    }

    public function __construct()
    {
        $this->bets = new ArrayCollection();
    }

    public function getBets(): Collection
    {
        return $this->bets;
    }

    public function getAwayOdds(): ?int
    {
        return $this->awayOdds;
    }

    public function setAwayOdds(int $awayOdds): self
    {
        $this->awayOdds = $awayOdds;
        return $this;
    }

    public function getHomeOdds(): ?int
    {
        return $this->homeOdds;
    }

    public function setHomeOdds(int $homeOdds): self
    {
        $this->homeOdds = $homeOdds;
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

    public function addBet(Bet $bet): self
    {
        if (!$this->bets->contains($bet)) {
            $this->bets[] = $bet;
            $bet->setGame($this);
        }

        return $this;
    }

    public function removeBet(Bet $bet): self
    {
        if ($this->bets->removeElement($bet)) {
            // set the owning side to null (unless already changed)
            if ($bet->getGame() === $this) {
                $bet->setGame(null);
            }
        }

        return $this;
    }
}
