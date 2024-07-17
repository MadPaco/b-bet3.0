<?php

namespace App\Repository;

use App\Entity\Bet;
use App\Entity\User;
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
    public function findBetsByWeeknumber($weekNumber)
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
}
