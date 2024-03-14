<?php
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use App\Entity\NflTeam;
use App\Entity\Bet;
use App\Entity\Odd;
use App\Entity\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $weekNumber;

    #[ORM\Column(type: 'date')]
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

    #[ORM\OneToOne(targetEntity: Odd::class, mappedBy: "game")]
    private $odds;

    #[ORM\OneToOne(targetEntity: Result::class, mappedBy: "game")]
    private $results;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
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

    public function __construct() {
        $this->bets = new ArrayCollection();
        $this->odds = new ArrayCollection();
        $this->results = new ArrayCollection();
    }
    
    public function getBets(): Collection
    {
        return $this->bets;
    }

    public function getOdds(): ?Odd
    {
        return $this->odds;
    }

    public function setOdds(Odd $odds): self
    {
        if ($odds->getGame() !== $this) {
            $odds->setGame($this);
        }

        $this->odds = $odds;
        return $this;
    }

    public function getResults(): ?Results
    {
        return $this->results;
    }

    public function setResults(Results $results): self
    {
        if ($results->getGame() !== $this) {
            $results->setGame($this);
        }

        $this->results = $results;
        return $this;
    }
}