<?php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetsRepository::class)]

class Bet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $gameID;

    #[ORM\Column(type: 'integer')]
    private $userID;

    #[ORM\Column(type: 'integer')]
    private $homePrediction;

    #[ORM\Column(type: 'integer')]
    private $awayPrediction;

    #[ORM\Column(type: 'integer')]
    private $points;

    #[ORM\Column(type: 'datetime')]
    private $lastEdit;

    #[ORM\Column(type: 'integer')]
    private $editCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameID(): ?int
    {
        return $this->gameID;
    }

    public function setGameID(int $gameID): self
    {
        $this->gameID = $gameID;
        return $this;
    }

    public function getUserID(): ?int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): self
    {
        $this->userID = $userID;
        return $this;
    }

    public function getHomePrediction(): ?int
    {
        return $this->homePrediction;
    }

    public function setHomePrediction(int $homePrediction): self
    {
        $this->homePrediction = $homePrediction;
        return $this;
    }

    public function getAwayPrediction(): ?int
    {
        return $this->awayPrediction;
    }

    public function setAwayPrediction(int $awayPrediction): self
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

?>