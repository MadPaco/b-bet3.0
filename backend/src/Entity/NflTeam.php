<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: "favTeam")]
    private $users;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: "homeTeam")]
    private $homeGames;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: "awayTeam")]
    private $awayGames;

    public function __construct() {
        $this->users = new ArrayCollection();
        $this->homeGames = new ArrayCollection();
        $this->awayGames = new ArrayCollection();
    }

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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getHomeGames(): Collection
    {
        return $this->homeGames;
    }

    public function getAwayGames(): Collection
    {
        return $this->awayGames;
    }
    
}
?>