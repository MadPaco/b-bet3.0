<?php

namespace App\Repository;

use App\Entity\Bet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bet::class);
    }

    public function findBetsByWeeknumber($weekNumber)
    {
        return $this->createQueryBuilder('bet')
            ->innerJoin('bet.game', 'game')
            ->where('game.weekNumber = :weekNumber')
            ->setParameter('weekNumber', $weekNumber)
            ->getQuery()
            ->getResult();
    }

    public function findNumberOfRegularSeasonBets(User $user)
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

    public function findLatestCompletedWeekNumber()
    // a week is considered completed if every bet in that week has a 
    // home and awayPrediction value that is not null
    // this is used to check for the early bird achievement
    {
        return $this->createQueryBuilder('bet')
            ->select('max(game.weekNumber)')
            ->innerJoin('bet.game', 'game')
            ->where('bet.homePrediction IS NOT NULL')
            ->andWhere('bet.awayPrediction IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
