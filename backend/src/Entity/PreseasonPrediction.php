<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\NflTeam;

#[ORM\Entity(repositoryClass: "App\Repository\PreseasonPredictionRepository")]
class PreseasonPrediction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: "preseasonPredictions")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $AFCChampion;

    #[ORM\Column(type: 'integer')]
    private $AFCChampionPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $NFCChampion;

    #[ORM\Column(type: 'integer')]
    private $NFCChampionPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $SuperBowlWinner;

    #[ORM\Column(type: 'integer')]
    private $SuperBowlWinnerPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mostPassingYards;

    #[ORM\Column(type: 'integer')]
    private $mostPassingYardsPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mostRushingYards;

    #[ORM\Column(type: 'integer')]
    private $mostRushingYardsPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $firstPick;

    #[ORM\Column(type: 'integer')]
    private $firstPickPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mostPointsScored;

    #[ORM\Column(type: 'integer')]
    private $mostPointsScoredPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $fewestPointsAllowed;

    #[ORM\Column(type: 'integer')]
    private $fewestPointsAllowedPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $highestMarginOfVictory;

    #[ORM\Column(type: 'integer')]
    private $highestMarginOfVictoryPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $oroy;

    #[ORM\Column(type: 'integer')]
    private $oroyPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $droy;

    #[ORM\Column(type: 'integer')]
    private $droyPoints;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mvp;

    #[ORM\Column(type: 'integer')]
    private $mvpPoints;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAFCChampion(): ?NflTeam
    {
        return $this->AFCChampion;
    }

    public function setAFCChampion(?NflTeam $AFCChampion): self
    {
        $this->AFCChampion = $AFCChampion;
        return $this;
    }

    public function getAFCChampionPoints(): ?int
    {
        return $this->AFCChampionPoints;
    }

    public function setAFCChampionPoints(int $AFCChampionPoints): self
    {
        $this->AFCChampionPoints = $AFCChampionPoints;
        return $this;
    }

    public function getNFCChampion(): ?NflTeam
    {
        return $this->NFCChampion;
    }

    public function setNFCChampion(?NflTeam $NFCChampion): self
    {
        $this->NFCChampion = $NFCChampion;
        return $this;
    }

    public function getNFCChampionPoints(): ?int
    {
        return $this->NFCChampionPoints;
    }

    public function setNFCChampionPoints(int $NFCChampionPoints): self
    {
        $this->NFCChampionPoints = $NFCChampionPoints;
        return $this;
    }

    public function getSuperBowlWinner(): ?NflTeam
    {
        return $this->SuperBowlWinner;
    }

    public function setSuperBowlWinner(?NflTeam $SuperBowlWinner): self
    {
        $this->SuperBowlWinner = $SuperBowlWinner;
        return $this;
    }

    public function getSuperBowlWinnerPoints(): ?int
    {
        return $this->SuperBowlWinnerPoints;
    }

    public function setSuperBowlWinnerPoints(int $SuperBowlWinnerPoints): self
    {
        $this->SuperBowlWinnerPoints = $SuperBowlWinnerPoints;
        return $this;
    }

    public function getMostPassingYards(): ?NflTeam
    {
        return $this->mostPassingYards;
    }

    public function setMostPassingYards(?NflTeam $mostPassingYards): self
    {
        $this->mostPassingYards = $mostPassingYards;
        return $this;
    }

    public function getMostPassingYardsPoints(): ?int
    {
        return $this->mostPassingYardsPoints;
    }

    public function setMostPassingYardsPoints(int $mostPassingYardsPoints): self
    {
        $this->mostPassingYardsPoints = $mostPassingYardsPoints;
        return $this;
    }

    public function getMostRushingYards(): ?NflTeam
    {
        return $this->mostRushingYards;
    }

    public function setMostRushingYards(?NflTeam $mostRushingYards): self
    {
        $this->mostRushingYards = $mostRushingYards;
        return $this;
    }

    public function getMostRushingYardsPoints(): ?int
    {
        return $this->mostRushingYardsPoints;
    }

    public function setMostRushingYardsPoints(int $mostRushingYardsPoints): self
    {
        $this->mostRushingYardsPoints = $mostRushingYardsPoints;
        return $this;
    }

    public function getFirstPick(): ?NflTeam
    {
        return $this->firstPick;
    }

    public function setFirstPick(?NflTeam $firstPick): self
    {
        $this->firstPick = $firstPick;
        return $this;
    }

    public function getFirstPickPoints(): ?int
    {
        return $this->firstPickPoints;
    }

    public function setFirstPickPoints(int $firstPickPoints): self
    {
        $this->firstPickPoints = $firstPickPoints;
        return $this;
    }

    public function getMostPointsScored(): ?NflTeam
    {
        return $this->mostPointsScored;
    }

    public function setMostPointsScored(?NflTeam $mostPointsScored): self
    {
        $this->mostPointsScored = $mostPointsScored;
        return $this;
    }

    public function getMostPointsScoredPoints(): ?int
    {
        return $this->mostPointsScoredPoints;
    }

    public function setMostPointsScoredPoints(int $mostPointsScoredPoints): self
    {
        $this->mostPointsScoredPoints = $mostPointsScoredPoints;
        return $this;
    }

    public function getFewestPointsAllowed(): ?NflTeam
    {
        return $this->fewestPointsAllowed;
    }

    public function setFewestPointsAllowed(?NflTeam $fewestPointsAllowed): self
    {
        $this->fewestPointsAllowed = $fewestPointsAllowed;
        return $this;
    }

    public function getFewestPointsAllowedPoints(): ?int
    {
        return $this->fewestPointsAllowedPoints;
    }

    public function setFewestPointsAllowedPoints(int $fewestPointsAllowedPoints): self
    {
        $this->fewestPointsAllowedPoints = $fewestPointsAllowedPoints;
        return $this;
    }

    public function getHighestMarginOfVictory(): ?NflTeam
    {
        return $this->highestMarginOfVictory;
    }

    public function setHighestMarginOfVictory(?NflTeam $highestMarginOfVictory): self
    {
        $this->highestMarginOfVictory = $highestMarginOfVictory;
        return $this;
    }

    public function getHighestMarginOfVictoryPoints(): ?int
    {
        return $this->highestMarginOfVictoryPoints;
    }

    public function setHighestMarginOfVictoryPoints(int $highestMarginOfVictoryPoints): self
    {
        $this->highestMarginOfVictoryPoints = $highestMarginOfVictoryPoints;
        return $this;
    }

    public function getOroy(): ?NflTeam
    {
        return $this->oroy;
    }

    public function setOroy(?NflTeam $oroy): self
    {
        $this->oroy = $oroy;
        return $this;
    }

    public function getOroyPoints(): ?int
    {
        return $this->oroyPoints;
    }

    public function setOroyPoints(int $oroyPoints): self
    {
        $this->oroyPoints = $oroyPoints;
        return $this;
    }

    public function getDroy(): ?NflTeam
    {
        return $this->droy;
    }

    public function setDroy(?NflTeam $droy): self
    {
        $this->droy = $droy;
        return $this;
    }

    public function getDroyPoints(): ?int
    {
        return $this->droyPoints;
    }

    public function setDroyPoints(int $droyPoints): self
    {
        $this->droyPoints = $droyPoints;
        return $this;
    }

    public function getMvp(): ?NflTeam
    {
        return $this->mvp;
    }

    public function setMvp(?NflTeam $mvp): self
    {
        $this->mvp = $mvp;
        return $this;
    }

    public function getMvpPoints(): ?int
    {
        return $this->mvpPoints;
    }

    public function setMvpPoints(int $mvpPoints): self
    {
        $this->mvpPoints = $mvpPoints;
        return $this;
    }
}
