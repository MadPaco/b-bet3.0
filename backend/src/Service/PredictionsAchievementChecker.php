<?php

namespace App\Service;

use App\Entity\Bet;
use App\Entity\Game;
use App\Entity\User;
use App\Entity\Achievement;
use App\Entity\UserAchievement;
use Doctrine\ORM\EntityManagerInterface;


// this class checks and awards achievements to users based on the number of placed predictions
// Seasoned Pro (50 predictions)
// Expert (100 predictions)
// Gridiron Guru (200 predictions)
// Hall of Famer (all regular season predictions)
// Early bird (place all predictions for the week 24hours before the first game)

class PredictionsAchievementChecker
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function hasAchievement(User $user, $achievement): bool
    {
        $userAchievement = $this->entityManager->getRepository(UserAchievement::class)->findOneBy(['user' => $user, 'achievement' => $achievement]);
        return $userAchievement !== null;
    }

    private function awardAchievement(User $user, $achievementName): void
    {

        $achievement = $this->entityManager->getRepository(Achievement::class)->findOneBy(['name' => $achievementName]);
        $newAchievement = new UserAchievement();
        $newAchievement->setUser($user);
        $newAchievement->setAchievement($achievement);
        $newAchievement->setDateEarned(new \DateTime());
        $this->entityManager->persist($newAchievement);
        $this->entityManager->flush();
    }

    private function checkAchievement(User $user, $achievementName, $predictionsCount, $threshold): bool
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

    private function checkSeasonedPro(User $user, $predictionsCount): bool
    {
        return $this->checkAchievement($user, 'Seasoned Pro', $predictionsCount, 50);
    }

    private function checkExpert(User $user, $predictionsCount): bool
    {
        return $this->checkAchievement($user, 'Expert', $predictionsCount, 100);
    }

    private function checkGridironGuru(User $user, $predictionsCount): bool
    {
        return $this->checkAchievement($user, 'Gridiron Guru', $predictionsCount, 200);
    }

    private function checkHallOfFamer(User $user, $predictionsCount): bool
    {
        return $this->checkAchievement($user, 'Hall of Famer', $predictionsCount, 272);
    }

    private function checkEarlyBird(User $user): void
    {
        $latestCompletedWeek = $this->entityManager->getRepository(Bet::class)->findLatestCompletedWeekNumber();
        $earliestGame = $this->entityManager->getRepository(Game::class)->getEarliestGameDate($latestCompletedWeek);
        $now = new \DateTime();
        $diff = $earliestGame->diff($now);
        if ($diff->days <= 1) {
            return;
        }
        if ($this->hasAchievement($user, 'Early Bird')) {
            return;
        }
        $this->awardAchievement($user, 'Early Bird');
    }




    public function checkAllAchievements(User $user)
    {

        $this->checkEarlyBird($user);

        $predictionsCount = count($this->entityManager->getRepository(Bet::class)->findBy(['user' => $user]));
        // 272 is the total number of regular season games
        // check how many regular season predictions the user has made
        $regularSeasonPredictionsCount = $this->entityManager->getRepository(Bet::class)->findNumberOfRegularSeasonBets($user);

        if (!$this->checkSeasonedPro($user, $predictionsCount)) {
            return;
        }
        if (!$this->checkExpert($user, $predictionsCount)) {
            return;
        }
        if (!$this->checkGridironGuru($user, $predictionsCount)) {
            return;
        }
        $this->checkHallOfFamer($user, $regularSeasonPredictionsCount);
    }
}
