<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Achievement;
use App\Entity\UserAchievement;
use Doctrine\ORM\EntityManagerInterface;


// base class for functions that both achievementCheckers use
abstract class AchievementCheckerBase
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function hasAchievement(User $user, $achievement): bool
    {
        $userAchievement = $this->entityManager->getRepository(UserAchievement::class)->findOneBy(['user' => $user, 'achievement' => $achievement]);
        return $userAchievement !== null;
    }

    protected function awardAchievement(User $user, $achievementName): void
    {
        $achievement = $this->entityManager->getRepository(Achievement::class)->findOneBy(['name' => $achievementName]);
        $newAchievement = new UserAchievement();
        $newAchievement->setUser($user);
        $newAchievement->setAchievement($achievement);
        $newAchievement->setDateEarned(new \DateTime());
        $this->entityManager->persist($newAchievement);
        $this->entityManager->flush();
    }

    protected function checkAchievement(User $user, $achievementName, $predictionsCount, $threshold): bool
    {
        if ($this->hasAchievement($user, $achievementName)) {
            return true;
        }

        if ($predictionsCount >= $threshold && !$this->hasAchievement($user, $achievementName)) {
            $this->awardAchievement($user, $achievementName);
            return true;
        } else {
            return false;
        }
    }
}
