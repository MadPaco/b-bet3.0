<?php

namespace App\Service;

use App\Entity\Bet;
use App\Entity\User;
use App\Repository\BetRepositoryInterface;
use App\Repository\GameRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;


// this class checks and awards achievements to users based on the number of placed predictions
// Seasoned Pro (50 predictions)
// Expert (100 predictions)
// Gridiron Guru (200 predictions)
// Hall of Famer (all regular season predictions)
// Early bird (place all predictions for the week 24hours before the first game)

class PredictionsAchievementChecker extends AchievementCheckerBase
{
    private $betRepository;
    private $gameRepository;

    public function __construct(EntityManagerInterface $entityManager, GameRepositoryInterface $gameRepository, BetRepositoryInterface $betRepository)
    {
        parent::__construct($entityManager);
        $this->betRepository = $betRepository;
        $this->gameRepository = $gameRepository;
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
        $latestCompletedWeek = $this->betRepository->findLatestCompletedWeekNumber($user);
        if ($latestCompletedWeek === 0) {
            return;
        }
        $earliestGame = $this->gameRepository->getEarliestGameDate($latestCompletedWeek);
        $now = new \DateTime();
        $diff = $earliestGame->diff($now);
        if ($diff->days < 1) {
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
