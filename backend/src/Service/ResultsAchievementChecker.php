<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Achievement;
use App\Entity\UserAchievement;
use App\Entity\Bet;
use App\Repository\UserAchievementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

// this class checks and awards achievements to users based on the results

// implement the following achievements:
// (hitting means scoring atleast 1 point)
// First Down - Correctly predict your first game - DONE
// Two-Minute Drill - Place a prediction at most 2 minutes before kickoff and hit on it
// Trick Play - Change a prediction at least 4 times in a week and score points
// Audible - Change the winner of a game 10 minutes before kickoff and win
// Pigskin Prophet – Hit on all games in a week
// Touchdown - Score 7 points in a week
// Pick Six - Hit on 6 games in a single week
// MVP of the Week - Have the highest score in a week
// Consistency is Key - Score points in 10 weeks
// Consistent Performer - Score at least 10 points in 5 consecutive weeks
// Deep Run - Score at least 10 points in 10 consecutive weeks
// Hot Streak - Score at least 15 points in 3 consecutive weeks
// Bench Warmer - Score less than 5 points in 5 consecutive weeks
// Headstart - Score the most points in the first 6 weeks
// Midseason Form - Score the most points in weeks 7-12
// Playoff Push - Score the most points in weeks 13-18
// Bowl Game Secured - Hit in more than 50% of the games in the regular season
// Sunday Funday - Hit on every game that is played on Sunday
// Pro Bowler - Score at least 100 points in total
// All Pro - Score 200 points in the season.
// Bench Warmer - Score less than 5 points in 5 consecutive weeks
// Slump Buster - End a streak of three weeks with less than 5 points by scoring more than 10 points in a week
// Underdog Lover - Predict an underdog to win and hit
// Nostradamus - Predict the exact score of a game correctly
// Upset Specialist - Predict a big upset (-300 odds or worse)
// Perfectly Balanced - Predict a game that ends in a tie
// Nail-Biter - Correctly predict the margin of 5 games where the margin of victory is 3 points or less
// Blowout Boss - Correctly predict the margin of 5 games where the margin of victory is 14 points or more
// Hometown Hero – Hit on all games for your favorite team in the regular season
// Super Bowl Prophet - Correctly predict the Super Bowl winner
// Primetime Player - Correctly predict all primetime games (games played after 2' clock german time) in a week
// Bye Week - Score 0 points in a week
// Aaron Rodgers 2023 - Hit on the Thursday night game, but lose every other game this week
// Fumble - change the winner of a match before the game and lose 
// Punt Return - Change a prediction and end up with fewer points than if you hadn’t changed it




class ResultsAchievementChecker
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function hasAchievement(User $user, Achievement $achievement): bool
    {
        $userAchievement = $this->entityManager->getRepository(UserAchievement::class)->findOneBy([
            'user' => $user,
            'achievement' => $achievement,
        ]);
        return $userAchievement !== null;
    }

    private function awardAchievement(User $user, Achievement $achievement): void
    {
        $newAchievement = new UserAchievement();
        $newAchievement->setUser($user);
        $newAchievement->setAchievement($achievement);
        $newAchievement->setDateEarned(new \DateTime());
        $this->entityManager->persist($newAchievement);
        $this->entityManager->flush();
    }

    private function checkFirstDown(User $user): void
    {
        $achievement = $this->entityManager->getRepository(Achievement::class)->findOneBy(['name' => 'First Down']);
        if (!$achievement) {
            return;
        }
        if (!$this->hasAchievement($user, $achievement)) {
            $betsWithHit = $this->entityManager->getRepository(Bet::class)->findHitsByUser($user);
            if (count($betsWithHit) > 0) {
                $this->awardAchievement($user, $achievement);
            } else return;
        }
    }

    private function checkTwoMinuteDrill(User $user): void
    {
        $achievement = $this->entityManager->getRepository(Achievement::class)->findOneBy(['name' => 'Two Minute Drill']);
        if (!$achievement) {
            return;
        }
        if (!$this->hasAchievement($user, $achievement)) {
            $twoMinuteDrillHits = $this->entityManager->getRepository(Bet::class)->findTwoMinuteDrillHit($user);
            if (count($twoMinuteDrillHits) > 0) {
                $this->awardAchievement($user, $achievement);
            } else return;
        }
    }

    private function checkAllAchievements(User $user): void
    {
        // list of all checks
        $achievementChecks = [
            [$this, 'checkFirstDown'],
            [$this, 'checkTwoMinuteDrill'],
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
