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

    public function findFinishedGames()
    {
        $qb = $this->createQueryBuilder('g')
            ->where('g.homeScore IS NOT NULL')
            ->andWhere('g.awayScore IS NOT NULL')
            ->getQuery();

        return $qb->getResult();
    }

    public function findLatestWeekWithResults()
    {
        $qb = $this->createQueryBuilder('g')
            ->select('MAX(g.weekNumber)')
            ->where('g.homeScore IS NOT NULL')
            ->andWhere('g.awayScore IS NOT NULL')
            ->getQuery();

        return $qb->getSingleScalarResult();
    }

    public function getWinsOfTeam(NflTeam $team)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->where('g.homeTeam = :team AND g.homeScore > g.awayScore')
            ->orWhere('g.awayTeam = :team AND g.awayScore > g.homeScore')
            ->setParameter('team', $team)
            ->getQuery();
        //return an array with the games where the given team won
        return $qb->getResult();
    }

    public function getEarliestGameDate($weeknumber)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('MIN(g.date)')
            ->where('g.weekNumber = :weeknumber')
            ->setParameter('weeknumber', $weeknumber)
            ->getQuery();

        $result = $qb->getSingleScalarResult();
        return $result ? new \DateTime($result) : null;
    }
}
