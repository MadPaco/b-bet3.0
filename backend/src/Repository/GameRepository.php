<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\NflTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findGamesWithSameDivisionAndConference(string $conference, string $division)
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin('g.homeTeam', 'home')
            ->innerJoin('g.awayTeam', 'away')
            ->where('home.division = :division')
            ->andWhere('home.conference = :conference')
            ->andWhere('away.division = :division')
            ->andWhere('away.conference = :conference')
            ->setParameter('conference', $conference)
            ->setParameter('division', $division)
            ->getQuery();
    
        return $qb->getResult();
    }

    public function findDivisionGamesForTeam(NflTeam $team)
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin('g.homeTeam', 'home')
            ->innerJoin('g.awayTeam', 'away')
            ->where('home = :team OR away = :team')
            ->andWhere('home.division = away.division')
            ->andWhere('home.conference = away.conference')
            ->andWhere('home.division = :division')
            ->andWhere('home.conference = :conference')
            ->setParameter('team', $team)
            ->setParameter('division', $team->getDivision())
            ->setParameter('conference', $team->getConference())
            ->getQuery();
    
        return $qb->getResult();
    }

}

?>