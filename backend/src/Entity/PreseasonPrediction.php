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

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $NFCChampion;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $SuperBowlWinner;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mostPassingYards;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mostRushingYards;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $firstPick;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mostPointsScored;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $fewestPointsAllowed;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $highestMarginOfVictory;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $oroy;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $droy;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $mvp;

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

    public function getNFCChampion(): ?NflTeam
    {
        return $this->NFCChampion;
    }

    public function setNFCChampion(?NflTeam $NFCChampion): self
    {
        $this->NFCChampion = $NFCChampion;
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

    public function getMostPassingYards(): ?NflTeam
    {
        return $this->mostPassingYards;
    }

    public function setMostPassingYards(?NflTeam $mostPassingYards): self
    {
        $this->mostPassingYards = $mostPassingYards;
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

    public function getFirstPick(): ?NflTeam
    {
        return $this->firstPick;
    }

    public function setFirstPick(?NflTeam $firstPick): self
    {
        $this->firstPick = $firstPick;
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

    public function getFewestPointsAllowed(): ?NflTeam
    {
        return $this->fewestPointsAllowed;
    }

    public function setFewestPointsAllowed(?NflTeam $fewestPointsAllowed): self
    {
        $this->fewestPointsAllowed = $fewestPointsAllowed;
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

    public function getOroy(): ?NflTeam
    {
        return $this->oroy;
    }

    public function setOroy(?NflTeam $oroy): self
    {
        $this->oroy = $oroy;
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

    public function getMvp(): ?NflTeam
    {
        return $this->mvp;
    }

    public function setMvp(?NflTeam $mvp): self
    {
        $this->mvp = $mvp;
        return $this;
    }
}
