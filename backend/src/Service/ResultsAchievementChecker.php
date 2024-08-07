<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\PreseasonPrediction;
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
// (DONE) Audible - Change the winner of a game 10 minutes before kickoff and win
// (DONE) Pigskin Prophet – Hit on all games in a week
// (DONE) Touchdown - Score 7 points in a week
// (DONE) Pick Six - Hit on 6 games in a single week
// (DONE) MVP of the Week - Have the highest score in a week
// (DONE) Consistency is Key - Score points in 10 weeks (does not have to be consecutive)
// (DONE) Consistent Performer - Score at least 10 points in 5 consecutive weeks
// (DONE) Deep Run - Score at least 10 points in 10 consecutive weeks
// (DINE) Hot Streak - Score at least 15 points in 3 consecutive weeks
// (DONE) Bench Warmer - Score less than 5 points in 5 consecutive weeks
// (DONE) Headstart - Score the most points in the first 6 weeks
// (DONE) Midseason Form - Score the most points in weeks 7-12
// (DONE) Playoff Push - Score the most points in weeks 13-18
// (DONE) Bowl Game Secured - Hit in more than 50% of the games in the regular season
// (DONE) Sunday Funday - Hit on every game that is played on Sunday in  a single week
// (DONE) Pro Bowler - Score at least 100 points in total
// (DONE) All Pro - Score 200 points in the season.
// (DONE) Slump Buster - End a streak of three weeks with less than 5 points by scoring more than 10 points in a week
// (DONE) Underdog Lover - Predict an underdog to win and hit
// (DONE) Nostradamus - Predict the exact score of a game correctly
// (DONE) Upset Specialist - Predict a big upset (-300 odds or worse)
// (DONE) Perfectly Balanced - Predict a game that ends in a tie
// (DONE) Nail-Biter - Correctly predict the margin of 5 games where the margin of victory is 3 points or less
// (DONE) Sweep - Correctly predict the margin of 5 games where the margin of victory is 14 points or more
// (DONE) Hometown Hero – Hit on all games for your favorite team in the regular season
// (DONE) Super Bowl Prophet - Correctly predict the Super Bowl matchup and winner
// (DONE) Primetime Player - Correctly predict all primetime games (games played after 1' clock german time) in a week
// (DONE) Bye Week - Score 0 points in a week
// (DONE) Aaron Rodgers 2023 - Hit on the Thursday night game, but lose every other game this week
// (DONE) Fumble - change the winner of a match before the game and lose 
// () Punt Return - Change a prediction and end up with fewer points than if you hadn’t changed it




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

    //helper function
    private function checkConsecutivePoints(User $user, int $minPoints, int $minStreak, bool $lessThan = false): bool
    {
        $latestWeek = $this->gameRepository->getLatestFinishedWeek();
        $pointsByWeek = $this->betRepository->getTotalPointsByUserForAllWeeks($user, 1, $latestWeek);
        $consecutiveWeeks = 0;
        foreach ($pointsByWeek as $points) {
            if (($lessThan && $points < $minPoints) || (!$lessThan && $points >= $minPoints)) {
                $consecutiveWeeks++;
                if ($consecutiveWeeks >= $minStreak) {
                    return true;
                }
            } else {
                $consecutiveWeeks = 0;
            }
        }

        return false;
    }

    private function checkFirstDown(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'First Down']);
        $betsWithHit = $this->betRepository->findHitsByUser($user);;
        if (count($betsWithHit) > 0) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkTwoMinuteDrill(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Two-Minute Drill']);
        $twoMinuteDrillHits = $this->betRepository->findTwoMinuteDrillHit($user);
        if (count($twoMinuteDrillHits) > 0) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkTrickPlay(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Trick Play']);
        if ($this->betRepository->hasTrickPlayHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkPigskinProphet(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Pigskin Prophet']);
        if ($this->betRepository->hasPigskinProphetHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
        return;
    }

    private function checkTouchdown(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Touchdown']);
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
        $this->awardAchievement($highestScoringUser, $achievement->getName());
    }

    private function checkBowlGameSecured(User $user): void
    {
        // in college football, a team must have a record of .500 or better
        // at the end of the season to be eligible for a bowl game
        // this means that a user must hit in more than 50% of the games
        // which is 136 hits in a season with 272 games
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Bowl Game Secured']);
        $numberOfHits = $this->betRepository->getCountOfAllHitsByUser($user);
        if ($numberOfHits < 136) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkProBowler(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Pro Bowler']);
        $totalPoints = $this->betRepository->getTotalPointsByUser($user);
        if ($totalPoints < 100 || !$totalPoints) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkAllPro(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'All Pro']);
        $totalPoints = $this->betRepository->getTotalPointsByUser($user);
        if ($totalPoints < 200 || !$totalPoints) {
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
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkUpsetSpecialist(User $user): void
    {
        //Predict a big upset (-300 odds or worse)   
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Upset Specialist']);
        if ($this->betRepository->hasUpsetHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    private function checkPerfectlyBalanced(User $user): void
    {
        //Predict a game that ends in a tie
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Perfectly Balanced']);
        if ($this->betRepository->hasPerfectlyBalancedHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    //Consistency is Key - Score points in 10 weeks
    private function checkConsistencyIsKey(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Consistency is Key']);
        $countOfWeeksWithPoints = 0;
        $latestWeek = $this->gameRepository->getLatestFinishedWeek();
        for ($week = 1; $week <= $latestWeek; $week++) {
            $points = $this->betRepository->getTotalPointsByUserForWeek($user, $week);
            if ($points > 0) {
                $countOfWeeksWithPoints++;
            }
        }
        if ($countOfWeeksWithPoints >= 10) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }
    //Headstart - Score the most points in the first 6 weeks
    private function checkHeadstart(): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Headstart']);
        $headstartWinner = $this->betRepository->getWinnerThroughWeeks(1, 6);
        if (!$this->gameRepository->isFinished(6) || !$achievement || !$headstartWinner) {
            return;
        }
        $this->awardAchievement($headstartWinner, $achievement->getName());
    }
    private function checkMidseasonForm(): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Midseason Form']);
        $midseasonFormWinner = $this->betRepository->getWinnerThroughWeeks(7, 12);
        if (!$this->gameRepository->isFinished(12) || !$midseasonFormWinner || $this->hasAchievement($midseasonFormWinner, $achievement)) {
            return;
        }
        $this->awardAchievement($midseasonFormWinner, $achievement->getName());
    }

    private function checkPlayoffPush(): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Playoff Push']);
        $playoffPushWinner = $this->betRepository->getWinnerThroughWeeks(13, 18);
        if (
            !$achievement
            || !$this->gameRepository->isFinished(18)
            || !$playoffPushWinner
            || $this->hasAchievement($playoffPushWinner, $achievement)
        ) {
            return;
        }
        $this->awardAchievement($playoffPushWinner, $achievement->getName());
    }

    private function checkDeepRun(User $user): bool
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Deep Run']);
        if (!$achievement || $this->hasAchievement($user, $achievement) || !$this->checkConsecutivePoints($user, 10, 10, false)) {
            return false;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkHotStreak(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Hot Streak']);
        if (!$achievement || $this->hasAchievement($user, $achievement) || !$this->checkConsecutivePoints($user, 15, 3, false)) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkBenchWarmer(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Bench Warmer']);
        if (!$achievement || $this->hasAchievement($user, $achievement) || !$this->checkConsecutivePoints($user, 5, 5, true)) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkConsistentPerformer(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Consistent Performer']);
        if (!$achievement || $this->hasAchievement($user, $achievement) || !$this->checkConsecutivePoints($user, 10, 5)) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkSlumpBuster(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Slump Buster']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $latestWeek = $this->gameRepository->getLatestFinishedWeek();
        $pointsByWeek = $this->betRepository->getTotalPointsByUserForAllWeeks($user, 1, $latestWeek);

        $consecutiveWeeks = 0;
        foreach ($pointsByWeek as $points) {
            if ($points < 5) {
                $consecutiveWeeks++;
            } else {
                if ($consecutiveWeeks >= 3 && $points > 10) {
                    $this->awardAchievement($user, $achievement->getName());
                }
                $consecutiveWeeks = 0;
            }
        }
        return;
    }

    private function checkByeWeek(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Bye Week']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        if ($this->hasAchievement($user, $achievement)) {
            return;
        }
        $pointsByWeek = $this->betRepository->getTotalPointsByUserForAllWeeks($user);
        foreach ($pointsByWeek as $week => $points) {
            if ($points === 0 && $this->gameRepository->isFinished($week)) {
                $this->awardAchievement($user, $achievement->getName());
                return;
            }
        }
    }

    private function checkUnderdogLover(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Underdog Lover']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        if ($this->betRepository->hasUnderdogLoverHit($user)) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    private function checkNailBiter(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Nail-Biter']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $nailbiterCount = $this->betRepository->getNailbiterHitCount($user);
        if ($nailbiterCount >= 5) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    public function checkSweep(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Sweep']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $blowoutCount = $this->betRepository->getSweepHitCount($user);
        if ($blowoutCount >= 5) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    public function checkHometownHero(User $user): void
    {
        // all teams play their last game in week 18, so any checks before this are useless
        if ($this->gameRepository->isFinished(18) === false) {
            return;
        }
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Hometown Hero']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $favTeam = $user->getFavTeam();
        if (!$favTeam) {
            error_log('User ' . $user->getUsername() . ' does not have a favorite team');
            return;
        }
        $favTeamHits = $this->betRepository->getTeamHits($user, $favTeam);
        // a team plays 17 games in a season
        if ($favTeamHits == 17) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    public function checkPrimetimePlayer(User $user): void
    {
        // get all primetime bets for a week
        // get all primetime games for a week
        // compare the count and check that all bets are hits

        $latestWeek = $this->gameRepository->getLatestFinishedWeek();
        $primetimeBets = $this->betRepository->getPrimetimeBetsForWeek($user, $latestWeek);
        $primetimeGames = $this->gameRepository->getPrimetimeGamesForWeek($latestWeek);
        if (count($primetimeBets) != count($primetimeGames) || !$primetimeBets || !$primetimeGames) {
            return;
        }
        foreach ($primetimeBets as $bet) {
            if ($bet->getPoints() == 0) {
                return;
            }
        }
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Primetime Player']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    public function checkSundayFunday(User $user): void
    {
        $latestWeek = $this->gameRepository->getLatestFinishedWeek();
        $sundayBets = $this->betRepository->getSundayBetsForWeek($user, $latestWeek);
        $sundayGames = $this->gameRepository->getSundayGamesForWeek($latestWeek);

        if (count($sundayBets) != count($sundayGames) || !$sundayBets || !$sundayGames) {
            return;
        }
        foreach ($sundayBets as $bet) {
            if ($bet->getPoints() == 0) {
                return;
            }
        }
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Sunday Funday']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
    }

    private function checkSuperBowlProphet(User $user)
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Super Bowl Prophet']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        // there are predictions for the AFC, NFC and superbowlchamp
        // hand out the achievement if all three are correct
        $prediction = $this->entityManager->getRepository(PreseasonPrediction::class)->findOneBy(['user' => $user]);
        if (!$prediction) {
            return;
        }
        if ($prediction->getAFCChampionPoints() > 0 && $prediction->getNFCChampionPoints() > 0 && $prediction->getSuperBowlWinnerPoints() > 0) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    private function checkArod(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Aaron Rodgers 2023']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $latestWeek = $this->gameRepository->getLatestFinishedWeek();
        $thursdayNightBet = $this->betRepository->getThursdayNightBet($user, $latestWeek);
        if (!$thursdayNightBet) {
            return;
        }
        $thursdayPoints = $thursdayNightBet->getPoints();
        // check if the user has lost every other game this week
        // if this is the case the total of the week is equal to the thursday night bet points
        $totalPoints = $this->betRepository->getTotalPointsByUserForWeek($user, $latestWeek);
        if ($totalPoints == $thursdayPoints) {
            $this->awardAchievement($user, $achievement->getName());
        }
    }

    private function checkAudible(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Audible']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $lastWeeksBets = $this->betRepository->findBetsByUserForWeek($user, $this->gameRepository->getLatestFinishedWeek());
        $lastWeeksBets = array_filter($lastWeeksBets, function ($bet) {
            return $bet->getPoints() > 0;
        });
        if (count($lastWeeksBets) == 0) {
            return;
        }
        // check that the winner has changed, indicated by the previousAwayPrediction adn previousHomePrediction
        // being different from the actual predictions and having the other team as the winner
        foreach ($lastWeeksBets as $bet) {
            $game = $bet->getGame();
            if (($bet->getHomePrediction() > $bet->getAwayPrediction() && $bet->getPreviousAwayPrediction() > $bet->getPreviousHomePrediction())
                || ($bet->getAwayPrediction() > $bet->getHomePrediction() && $bet->getPreviousHomePrediction() > $bet->getPreviousAwayPrediction())
            ) {
                // check that the lastEdit is within 10 minutes of the kickoff
                $lastEdit = $bet->getLastEdit();
                $kickoff = $game->getDate();
                $diff = $lastEdit->diff($kickoff);
                if ($diff->i <= 10) {
                    $this->awardAchievement($user, $achievement->getName());
                    return;
                }
            }
        };
        return;
    }

    public function checkFumble(User $user): void
    {
        // change the winner of a match before the game and lose

        $achievement = $this->achievementRepository->findOneBy(['name' => 'Fumble']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $lastWeeksBets = $this->betRepository->findBetsByUserForWeek($user, $this->gameRepository->getLatestFinishedWeek());
        $lastWeeksBets = array_filter($lastWeeksBets, function ($bet) {
            return $bet->getPoints() == 0;
        });
        foreach ($lastWeeksBets as $bet) {
            if (($bet->getHomePrediction() > $bet->getAwayPrediction() && $bet->getPreviousAwayPrediction() > $bet->getPreviousHomePrediction())
                || ($bet->getAwayPrediction() > $bet->getHomePrediction() && $bet->getPreviousHomePrediction() > $bet->getPreviousAwayPrediction())
            ) {
                $this->awardAchievement($user, $achievement->getName());
                return;
            }
        }
    }

    public function checkPuntReturn(User $user): void
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Punt Return']);
        if (!$achievement || $this->hasAchievement($user, $achievement)) {
            return;
        }
        $this->awardAchievement($user, $achievement->getName());
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
            [$this, 'checkPerfectlyBalanced'],
            [$this, 'checkConsistencyIsKey'],
            [$this, 'checkHeadstart'],
            [$this, 'checkMidseasonForm'],
            [$this, 'checkPlayoffPush'],
            [$this, 'checkDeepRun'],
            [$this, 'checkHotStreak'],
            [$this, 'checkBenchWarmer'],
            [$this, 'checkConsistentPerformer'],
            [$this, 'checkSlumpBuster'],
            [$this, 'checkByeWeek'],
            [$this, 'checkUnderdogLover'],
            [$this, 'checkNailBiter'],
            [$this, 'checkSweep'],
            [$this, 'checkHometownHero'],
            [$this, 'checkPrimetimePlayer'],
            [$this, 'checkSundayFunday'],
            [$this, 'checkSuperBowlProphet'],
            [$this, 'checkArod'],
            [$this, 'checkAudible']
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


    //Correctly predict all primetime games (games played after 2' clock german time) in a week
    //Note: There is a week in the season where germany switches to winter time
    //but the US don't until a week later, so the time difference is 1 hour less

}
