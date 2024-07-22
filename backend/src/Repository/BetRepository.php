<?php

namespace App\Repository;

use App\Entity\Bet;
use App\Entity\User;
use App\Entity\NflTeam;
use App\Repository\GameRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BetRepository extends ServiceEntityRepository implements BetRepositoryInterface
{
    private $gameRepository;

    public function __construct(ManagerRegistry $registry, GameRepositoryInterface $gameRepository)
    {
        parent::__construct($registry, Bet::class);
        $this->gameRepository = $gameRepository;
    }

    // helper functions
    public function getCountOfHitsByUserForGivenWeek(User $user, $week): int
    {
        $query = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('bet.points > 0')
            ->andWhere('game.weekNumber = :week')
            ->setParameter('user', $user)
            ->setParameter('week', $week)
            ->getQuery();

        $result = $query->getSingleScalarResult();
        return $result;
    }

    public function getNumberOfBetsInWeek(User $user, $week): int
    {
        return $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('game.weekNumber = :week')
            ->setParameter('user', $user)
            ->setParameter('week', $week)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getHighestScoringUser(int $week): ?User
    {
        $query = $this->createQueryBuilder('bet')
            ->select('IDENTITY(bet.user) as userId')
            ->addSelect('SUM(bet.points) as points')
            ->innerJoin('bet.game', 'game')
            ->where('game.weekNumber = :week')
            ->groupBy('bet.user')
            ->orderBy('points', 'DESC')
            ->setMaxResults(1)
            ->setParameter('week', $week)
            ->getQuery();

        $result = $query->getOneOrNullResult();
        if ($result === null) {
            return null;
        }

        return $this->getEntityManager()->getRepository(User::class)->find($result['userId']);
    }

    public function getWinnerThroughWeeks(int $begin, int $end): ?User
    {
        $query = $this->createQueryBuilder('bet')
            ->select('IDENTITY(bet.user) as userId')
            ->addSelect('SUM(bet.points) as points')
            ->innerJoin('bet.game', 'game')
            ->where('game.weekNumber >=:begin')
            ->andWhere('game.weekNumber <=:end')
            ->groupBy('bet.user')
            ->orderBy('points', 'DESC')
            ->setParameter('begin', $begin)
            ->setParameter('end', $end)
            ->setMaxResults(1)
            ->getQuery();

        $result = $query->getOneOrNullResult();
        if ($result === null) {
            return null;
        }

        return $this->getEntityManager()->getRepository(User::class)->find($result['userId']);
    }

    // functions used by ResultsAchievementChecker
    // *******************************************

    // return all bet ids where a user has atleast 1 point
    // used to calculate stats (hitrate etc)
    public function findHitsByUser(User $user)
    {
        return $this->createQueryBuilder('bet')
            ->select('bet')
            ->where('bet.user =:user')
            ->andwhere('bet.points != 0')
            ->setParameter('user', $user)
            ->getQuery()->getResult() ?? [];
    }

    public function findTwoMinuteDrillHit(User $user)
    {
        return $this->createQueryBuilder('bet')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('bet.points >= 1')
            ->andWhere('bet.lastEdit >= DATE_SUB(game.date, 2, \'MINUTE\')')
            ->andWhere('bet.lastEdit <= game.date')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function hasTrickPlayHit(User $user): bool
    {
        $queryResult = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->where('bet.editCount >= 4')
            ->andWhere('bet.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $queryResult > 0;
    }

    // returns true if the user has a week with a hit in every game
    public function hasPigskinProphetHit(User $user): bool
    {
        $weeks = $this->gameRepository->getWeeks();

        foreach ($weeks as $week) {
            $weekNumber = $week;
            $hits = $this->getCountOfHitsByUserForGivenWeek($user, $weekNumber);
            $totalGames = $this->gameRepository->getNumberOfGamesForGivenWeek($weekNumber);
            if ($hits == $totalGames && $totalGames > 0) {
                return true;
            }
        }
        return false;
    }


    // functions used by PredictionsAchievementChecker
    // *******************************************

    // return all bets for a given week
    public function findBetsByWeeknumber($weekNumber): array
    {
        return $this->createQueryBuilder('bet')
            ->innerJoin('bet.game', 'game')
            ->where('game.weekNumber = :weekNumber')
            ->setParameter('weekNumber', $weekNumber)
            ->getQuery()
            ->getResult();
    }

    // return the number of predictions placed in the regular season
    // used to check wether a user is eligible for the Hall of Famer Achievement
    public function findNumberOfRegularSeasonBets(User $user): int
    {
        return $this->createQueryBuilder('bet')
            ->select('count(bet.id)')
            ->innerJoin('bet.game', 'game')
            // regular season is from week 1 to 18
            ->where('game.weekNumber < 19')
            ->andWhere('bet.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getCountOfAllHitsByUser(User $user): int
    {
        $result = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->where('bet.user = :user')
            ->andWhere('bet.points > 0')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        // if no hits are found, return 0
        if (!$result) {
            return 0;
        }
        return $result;
    }

    public function getTotalPointsByUser(User $user): int
    {
        $result = $this->createQueryBuilder('bet')
            ->select('SUM(bet.points)')
            ->where('bet.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        // if no bets are found, return 0
        if (!$result) {
            return 0;
        }
        return $result;
    }

    public function getTotalPointsByUserForWeek(User $user, int $week): int
    {
        $result = $this->createQueryBuilder('bet')
            ->select('SUM(bet.points)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user =:user')
            ->andWhere('game.weekNumber =:week')
            ->setParameter('user', $user)
            ->setParameter('week', $week)
            ->getQuery()
            ->getSingleScalarResult();
        // if no bets are found, return 0
        if (!$result) {
            return 0;
        }
        return $result;
    }

    // return the last week where a user placed all bets
    // return 0 if no week is found
    public function findLatestCompletedWeekNumber(User $user): int
    {
        $latestCompletedWeek = 0;
        // Get number of games for all weeks
        $weeks = $this->gameRepository->getWeeks();

        foreach ($weeks as $week) {
            $totalGames = $this->gameRepository->getNumberOfGamesForGivenWeek($week);
            $numberOfBets = $this->getNumberOfBetsInWeek($user, $week);

            if ($numberOfBets == $totalGames && $totalGames > 0) {
                $latestCompletedWeek = $week;
            }
        }
        return $latestCompletedWeek;
    }

    public function hasUpsetHit(User $user): bool
    {
        $queryResult = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('bet.points > 0')
            ->andWhere('(
                (game.homeScore < game.awayScore AND bet.homePrediction < bet.awayPrediction AND game.awayOdds <= -300)
                OR
                (game.homeScore > game.awayScore AND bet.homePrediction > bet.awayPrediction AND game.homeOdds <= -300)
            )')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $queryResult > 0;
    }
    public function hasPerfectlyBalancedHit(User $user): bool
    {
        $queryResult = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('bet.points > 0')
            ->andWhere('bet.homePrediction = bet.awayPrediction AND game.homeScore = game.awayScore')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        return $queryResult > 0;
    }

    public function getTotalPointsByUserForAllWeeks(User $user): array
    {
        $weeks = $this->gameRepository->getWeeks();
        $points = [];
        foreach ($weeks as $week) {
            $points[$week] = $this->getTotalPointsByUserForWeek($user, $week);
        }
        return $points;
    }

    //user predicts the underdog to win and the underdog wins
    public function hasUnderdogLoverHit(User $user): bool
    {
        $queryResult = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user =:user')
            ->andWhere('bet.points > 0')
            ->andWhere('(
                (game.homeScore < game.awayScore AND bet.homePrediction < bet.awayPrediction AND game.homeOdds > game.awayOdds)
                OR
                (game.homeScore > game.awayScore AND bet.homePrediction > bet.awayPrediction AND game.awayOdds > game.homeOdds)
            )')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $queryResult > 0;
    }

    public function getNailbiterHitCount(User $user): int
    {
        $queryResult = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('bet.points > 0')
            ->andWhere('ABS(game.homeScore - game.awayScore) <= 3')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $queryResult;
    }

    public function getSweepHitCount(User $user): int
    {
        $queryResult = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('bet.points > 0')
            ->andWhere('ABS(game.homeScore - game.awayScore) >= 21')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $queryResult;
    }

    public function getTeamHits(User $user, NflTeam $team): int
    {
        $query = $this->createQueryBuilder('bet')
            ->select('COUNT(bet.id)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user =:user')
            ->andWhere('bet.points > 0')
            // AND game.weekNumber < 19 is used to exclude playoff games
            ->andWhere('(
                (game.homeTeam =:team AND game.homeScore > game.awayScore AND game.weekNumber < 19)
                OR
                (game.awayTeam =:team AND game.awayScore > game.homeScore AND game.weekNumber < 19)
            )')
            ->setParameter('user', $user)
            ->setParameter('team', $team)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getPrimetimeBetsForWeek(User $user, int $week): array
    {
        // primetime condition
        // there are some games played at 1/3 am german time
        // while technically not primetime, they are included in the query
        // I did this after asking some users wether they consider those to be primetime games

        // so we have to return all bets that have a game.date between 1 am and 4 am german time

        return $this->createQueryBuilder('bet')
            ->select('bet')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user =:user')
            ->andWhere('game.weekNumber =:week')
            ->andWhere('(
                (HOUR(game.date) >= 1 AND HOUR(game.date) < 4)
            )')
            ->setParameter('user', $user)
            ->setParameter('week', $week)
            ->getQuery()
            ->getResult();
    }

    public function getSundayBetsForWeek(User $user, int $week): array
    {
        return $this->createQueryBuilder('bet')
            ->select('bet')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user =:user')
            ->andWhere('game.weekNumber =:week')
            ->andWhere('((HOUR(game.date) > 12 AND HOUR(game.date) < 24 AND DAYOFWEEK(game.date) = 1)
                    OR 
                    (HOUR(game.date) > 0 AND HOUR(game.date) < 4 AND DAYOFWEEK(game.date) = 2))')
            ->setParameter('user', $user)
            ->setParameter('week', $week)
            ->getQuery()
            ->getResult();
    }
}
