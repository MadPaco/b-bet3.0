<?php
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

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

    #[ORM\Column(type: 'integer')]
    private $homeTeam;

    #[ORM\Column(type: 'integer')]
    private $awayTeam;

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

    public function getHomeTeam(): ?int
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(int $homeTeam): self
    {
        $this->homeTeam = $homeTeam;
        return $this;
    }

    public function getAwayTeam(): ?int
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(int $awayTeam): self
    {
        $this->awayTeam = $awayTeam;
        return $this;
    }
}