<?php

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: UserAchievementRepository::class)]

class UserAchievement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $userId;

    #[ORM\Column(type: 'integer')]
    private $achievementId;

    #[ORM\Column(type: 'datetime')]
    private $dateEarned;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getAchievementId(): ?int
    {
        return $this->achievementId;
    }

    public function setAchievementId(int $achievementId): self
    {
        $this->achievementId = $achievementId;
        return $this;
    }

    public function getDateEarned(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDateEarned(DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }
}

?>