<?php

namespace App\Repository;

use App\Entity\Bet;
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
        return $this->createQueryBuilder('b')
            ->innerJoin('b.game', 'g')
            ->where('g.weekNumber = :weekNumber')
            ->setParameter('weekNumber', $weekNumber)
            ->getQuery()
            ->getResult();
    }

}

?>