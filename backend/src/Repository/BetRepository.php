<?php

namespace App\Repository;

use App\Entity\Bet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BetRepository extends ServiceEntityRepository implements BetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bet::class);
    }


    // functions used by ResultsAchievementChecker
    // *******************************************
    public function findHitsByUser(User $user)
    {
        return $this->createQueryBuilder('bet')
            ->select('bet.id')
            ->where('bet.user =:user')
            ->andwhere('bet.points != 0')
            ->setParameter('user', $user)
            ->getQuery()->getResult();
    }

    public function findTwoMinuteDrillHit(User $user)
    {
        return $this->createQueryBuilder('bet')
            ->innerJoin('App\Entity\Game', 'game', 'WITH', 'bet.game_id = game.id')
            ->where('bet.user_id = :user')
            ->andWhere('bet.points >= 1')
            ->andWhere('bet.last_edit >= DATE_SUB(game.date, INTERVAL 2 MINUTE)')
            ->andWhere('bet.last_edit <= game.date')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult();
    }

    // functions used by PredictionsAchievementChecker
    // *******************************************
    public function findBetsByWeeknumber($weekNumber)
    {
        return $this->createQueryBuilder('bet')
            ->innerJoin('bet.game', 'game')
            ->where('game.weekNumber = :weekNumber')
            ->setParameter('weekNumber', $weekNumber)
            ->getQuery()
            ->getResult();
    }

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

    public function findLatestCompletedWeekNumber(User $user): int
    {
        // Get entity manager
        $em = $this->getEntityManager();

        // Get number of games for all weeks
        $numberOfGames = $em->createQueryBuilder()
            ->select('game.weekNumber, COUNT(game.id) AS numGames')
            ->from('App\Entity\Game', 'game')
            ->groupBy('game.weekNumber')
            ->getQuery()
            ->getResult();

        // Get number of predictions by user for all weeks
        $numberOfPredictions = $this->createQueryBuilder('bet')
            ->select('game.weekNumber, COUNT(bet.id) AS numPredictions')
            ->innerJoin('bet.game', 'game')
            ->where('bet.user = :user')
            ->andWhere('bet.homePrediction IS NOT NULL')
            ->andWhere('bet.awayPrediction IS NOT NULL')
            ->setParameter('user', $user)
            ->groupBy('game.weekNumber')
            ->getQuery()
            ->getResult();

        // Convert results to associative arrays
        $gamesPerWeek = [];
        foreach ($numberOfGames as $game) {
            $gamesPerWeek[$game['weekNumber']] = $game['numGames'];
        }

        $predictionsPerWeek = [];
        foreach ($numberOfPredictions as $prediction) {
            $predictionsPerWeek[$prediction['weekNumber']] = $prediction['numPredictions'];
        }

        // Find the highest week where the number of predictions equals the number of games
        $latestCompletedWeek = 0;
        foreach ($gamesPerWeek as $weekNumber => $numGames) {
            if (isset($predictionsPerWeek[$weekNumber]) && $predictionsPerWeek[$weekNumber] == $numGames) {
                if ($weekNumber > $latestCompletedWeek) {
                    $latestCompletedWeek = $weekNumber;
                }
            }
        }

        return $latestCompletedWeek;
    }
}
