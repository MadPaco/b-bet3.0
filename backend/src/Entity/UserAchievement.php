<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Achievement;

#[ORM\Entity(repositoryClass: UserAchievementRepository::class)]

class UserAchievement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Achievement::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $achievement;

    #[ORM\Column(type: 'datetime')]
    private $dateEarned;

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

    public function getAchievement(): ?Achievement
    {
        return $this->achievement;
    }

    public function setAchievement(Achievement $achievement): self
    {
        $this->achievement = $achievement;
        return $this;
    }

    public function getDateEarned(): ?DateTimeInterface
    {
        return $this->dateEarned;
    }

    public function setDateEarned(DateTimeInterface $date): self
    {
        $this->dateEarned = $date;
        return $this;
    }
}

?>