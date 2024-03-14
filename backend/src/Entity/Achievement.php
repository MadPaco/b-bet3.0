<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\UserAchievement;

#[ORM\Entity(repositoryClass: AchievementRepository::class)]
class Achievement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text')]
    private $description;    

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[ORM\OneToMany(targetEntity: UserAchievement::class, mappedBy: "achievement")]
    private $userAchievements;


    public function getIt(): ?int
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function __construct() {
        $this->userAchievements = new ArrayCollection();
    }

    public function getUserAchievements(): Collection
    {
        return $this->userAchievements;
    }
}

?>