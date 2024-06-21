<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\NflTeamRepository")]
class NflTeam
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "string", length: 255)]
    private $shorthandName;

    #[ORM\Column(type: "string", length: 255)]
    private $logo;

    #[ORM\Column(type: "string", length: 255)]
    private $location;

    #[ORM\Column(type: "string", length: 255)]
    private $division;

    #[ORM\Column(type: "string", length: 255)]
    private $conference;

    #[ORM\Column(type: "string", length: 255)]
    private $primaryColor;

    #[ORM\Column(type: "integer", length: 255)]
    private $wins;

    #[ORM\Column(type: "integer", length: 255)]
    private $losses;

    #[ORM\Column(type: "integer", length: 255)]
    private $ties;

    #[ORM\Column(type: "integer", length: 255)]
    private $pointsFor;

    #[ORM\Column(type: "integer", length: 255)]
    private $pointsAgainst;

    #[ORM\Column(type: "integer", length: 255)]
    private $netPoints;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getShorthandName(): ?string
    {
        return $this->shorthandName;
    }

    public function setShorthandName(string $shorthandName): self
    {
        $this->shorthandName = $shorthandName;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;
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

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function setDivision(string $division): self
    {
        $this->division = $division;
        return $this;
    }

    public function getConference(): ?string
    {
        return $this->conference;
    }

    public function setConference(string $conference): self
    {
        $this->conference = $conference;
        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    public function setPrimaryColor(string $primaryColor): self
    {
        $this->primaryColor = $primaryColor;
        return $this;
    }

    public function getWins(): ?int
    {
        return $this->wins;
    }

    public function setWins(int $wins): self
    {
        $this->wins = $wins;
        return $this;
    }

    public function getLosses(): ?int
    {
        return $this->losses;
    }

    public function setLosses(int $losses): self
    {
        $this->losses = $losses;
        return $this;
    }

    public function getTies(): ?int
    {
        return $this->ties;
    }

    public function setTies(int $ties): self
    {
        $this->ties = $ties;
        return $this;
    }

    public function getPointsFor(): ?int
    {
        return $this->pointsFor;
    }

    public function setPointsFor(int $pointsFor): self
    {
        $this->pointsFor = $pointsFor;
        return $this;
    }

    public function getPointsAgainst(): ?int
    {
        return $this->pointsAgainst;
    }

    public function setPointsAgainst(int $pointsAgainst): self
    {
        $this->pointsAgainst = $pointsAgainst;
        return $this;
    }

    public function getNetPoints(): ?int
    {
        return $this->netPoints;
    }

    public function setNetPoints(int $netPoints): self
    {
        $this->netPoints = $netPoints;
        return $this;
    }
}
?>