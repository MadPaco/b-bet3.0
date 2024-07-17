<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\AchievementRepository;
use App\Repository\BetRepositoryInterface;
use App\Repository\GameRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

// this class checks and awards achievements to users based on the results

// implement the following achievements:
// (hitting means scoring atleast 1 point)
// (DONE) First Down - Correctly predict your first game 
// (DONE) Two-Minute Drill - Place a prediction at most 2 minutes before kickoff and hit on it 
// (DONE) Trick Play - Change a prediction at least 4 times in a week and score points
// Audible - Change the winner of a game 10 minutes before kickoff and win
// (DONE) Pigskin Prophet – Hit on all games in a week
// (DONE) Touchdown - Score 7 points in a week
// (DONE) Pick Six - Hit on 6 games in a single week
// (DONE) MVP of the Week - Have the highest score in a week
// Consistency is Key - Score points in 10 weeks
// Consistent Performer - Score at least 10 points in 5 consecutive weeks
// Deep Run - Score at least 10 points in 10 consecutive weeks
// Hot Streak - Score at least 15 points in 3 consecutive weeks
// Bench Warmer - Score less than 5 points in 5 consecutive weeks
// Headstart - Score the most points in the first 6 weeks
// Midseason Form - Score the most points in weeks 7-12
// Playoff Push - Score the most points in weeks 13-18
// (DONE) Bowl Game Secured - Hit in more than 50% of the games in the regular season
// Sunday Funday - Hit on every game that is played on Sunday in  a single week
// (DONE) Pro Bowler - Score at least 100 points in total
// (DONE) All Pro - Score 200 points in the season.
// Bench Warmer - Score less than 5 points in 5 consecutive weeks
// Slump Buster - End a streak of three weeks with less than 5 points by scoring more than 10 points in a week
// Underdog Lover - Predict an underdog to win and hit
// (DONE) Nostradamus - Predict the exact score of a game correctly
// (DONE) Upset Specialist - Predict a big upset (-300 odds or worse)
// (DONE)Perfectly Balanced - Predict a game that ends in a tie
// Nail-Biter - Correctly predict the margin of 5 games where the margin of victory is 3 points or less
// Blowout Boss - Correctly predict the margin of 5 games where the margin of victory is 14 points or more
// Hometown Hero – Hit on all games for your favorite team in the regular season
// Super Bowl Prophet - Correctly predict the Super Bowl winner
// Primetime Player - Correctly predict all primetime games (games played after 2' clock german time) in a week
// Bye Week - Score 0 points in a week
// Aaron Rodgers 2023 - Hit on the Thursday night game, but lose every other game this week
// Fumble - change the winner of a match before the game and lose 
// Punt Return - Change a prediction and end up with fewer points than if you hadn’t changed it




class ResultsAchievementChecker extends AchievementCheckerBase
{
    private $betRepository;
    private $achievementRepository;
    private $gameRepository;

    public function __construct(EntityManagerInterface $entityManager, BetRepositoryInterface $betRepository, AchievementRepository $achievementRepository, GameRepositoryInterface $gameRepository)
    {
        parent::__construct($entityManager);
        $this->betRepository = $betRepository;
        $this->achievementRepository = $achievementRepository;
        $this->gameRepository = $gameRepository;
    }

    private function checkFirstDown(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'First Down']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }

        $betsWithHit = $this->betRepository->findHitsByUser($user);;
        if (count($betsWithHit) > 0) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkTwoMinuteDrill(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Two-Minute Drill']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        $twoMinuteDrillHits = $this->betRepository->findTwoMinuteDrillHit($user);
        if (count($twoMinuteDrillHits) > 0) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkTrickPlay(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Trick Play']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        if ($this->betRepository->hasTrickPlayHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkPigskinProphet(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Pigskin Prophet']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        if ($this->betRepository->hasPigskinProphetHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkTouchdown(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Touchdown']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        $betsWithHit = $this->betRepository->findHitsByUser($user);
        $points = 0;
        foreach ($betsWithHit as $bet) {
            $points += $bet->getPoints();
        }
        if ($points >= 7) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkPickSix(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Pick Six']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        $latestWeek = $this->gameRepository->findLatestWeekWithResults();
        if ($latestWeek === 0) {
            return;
        }
        $countOfHits = $this->betRepository->getCountOfHitsByUserForGivenWeek($user, $latestWeek);
        if ($countOfHits >= 6) {
            $this->awardAchievement($user, $achievement->getName());
        }

        return;
    }

    private function checkMVPOfTheWeek(): void
    {
        $latestWeek = $this->gameRepository->findLatestWeekWithResults();
        if (!$this->gameRepository->isFinished($latestWeek)) {
            return;
        }
        $highestScoringUser = $this->betRepository->getHighestScoringUser($latestWeek);
        if (!$highestScoringUser) {
            return;
        }
        $achievement = $this->achievementRepository->findOneBy(['name' => 'MVP of the Week']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($highestScoringUser, $achievement)) {
            return;
        }
        $this->awardAchievement($highestScoringUser, $achievement->getName());
    }

    public function checkBowlGameSecured(User $user): void
    {
        // in college football, a team must have a record of .500 or better
        // at the end of the season to be eligible for a bowl game
        // this means that a user must hit in more than 50% of the games
        // which is 136 hits in a season with 272 games
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Bowl Game Secured']);
        $numberOfHits = $this->betRepository->getCountOfAllHitsByUser($user);
        if ($this->hasAchievement($user, $achievement) || $numberOfHits < 136) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    public function checkProBowler(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Pro Bowler']);
        $totalPoints = $this->betRepository->getTotalPointsByUser($user);
        if ($this->hasAchievement($user, $achievement) || $totalPoints < 100 || !$totalPoints) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    public function checkAllPro(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'All Pro']);
        $totalPoints = $this->betRepository->getTotalPointsByUser($user);
        if ($this->hasAchievement($user, $achievement) || $totalPoints < 200 || !$totalPoints) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }


    public function checkNostradamus(User $user): void
    {
        // this will be invoked when a 5 point bet has been detected in the
        // update points function in the ResultsController to avoid
        // running this check for every user every week
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Nostradamus']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    public function checkUpsetSpecialist(User $user): void
    {
        //Predict a big upset (-300 odds or worse)   
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Upset Specialist']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        if ($this->betRepository->hasUpsetHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    public function checkPerfectlyBalanced(User $user): void
    {
        //Predict a game that ends in a tie
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Perfectly Balanced']);
        if (!$achievement) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        if ($this->betRepository->hasPerfectlyBalancedHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    private function checkAllAchievements(User $user): void
    {
        // list of all checks
        $achievementChecks = [
            [$this, 'checkFirstDown'],
            [$this, 'checkTwoMinuteDrill'],
            [$this, 'checkTrickPlay'],
            [$this, 'checkPigskinProphet'],
            [$this, 'checkTouchdown'],
            [$this, 'checkPickSix'],
            [$this, 'checkMVPOfTheWeek'],
            [$this, 'checkBowlGameSecured'],
            [$this, 'checkProBowler'],
            [$this, 'checkAllPro'],
            [$this, 'checkUpsetSpecialist'],
        ];

        foreach ($achievementChecks as $check) {
            $check($user);
        }
    }

    public function checkAllAchievementsForAllUsers(): void
    {
        $allUsers = $this->entityManager->getRepository(User::class)->findAll();
        foreach ($allUsers as $user) {
            $this->checkAllAchievements($user);
        }
    }
}
