<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\NflTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository implements GameRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getLatestFinishedWeek(): int
    {
        $qb = $this->createQueryBuilder('g')
            ->select('MAX(g.weekNumber)')
            ->where('g.homeScore IS NOT NULL')
            ->andWhere('g.awayScore IS NOT NULL')
            ->getQuery();

        $result = $qb->getSingleScalarResult();

        if ($result === null) {
            return 0;
        }

        if (!$this->isFinished($result)) {
            return $result - 1;
        }
        return $result;
    }

    public function findGamesWithSameDivisionAndConference(string $conference, string $division): array
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

    public function findDivisionGamesForTeam(NflTeam $team): array
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

    public function findFinishedGames(): array
    {
        $qb = $this->createQueryBuilder('g')
            ->where('g.homeScore IS NOT NULL')
            ->andWhere('g.awayScore IS NOT NULL')
            ->getQuery();

        return $qb->getResult();
    }

    public function findLatestWeekWithResults(): int
    {
        $qb = $this->createQueryBuilder('g')
            ->select('MAX(g.weekNumber)')
            ->where('g.homeScore IS NOT NULL')
            ->andWhere('g.awayScore IS NOT NULL')
            ->getQuery();

        $result = $qb->getSingleScalarResult();

        return $result !== null ? (int) $result : 0;
    }

    public function getWinsOfTeam(NflTeam $team): int
    {
        $qb = $this->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->where('g.homeTeam = :team AND g.homeScore > g.awayScore')
            ->orWhere('g.awayTeam = :team AND g.awayScore > g.homeScore')
            ->setParameter('team', $team)
            ->getQuery();
        // Return a single scalar result (the count of wins)
        return (int) $qb->getSingleScalarResult();
    }

    public function getEarliestGameDate($weeknumber): \DateTime
    {
        $qb = $this->createQueryBuilder('g')
            ->select('MIN(g.date)')
            ->where('g.weekNumber = :weeknumber')
            ->setParameter('weeknumber', $weeknumber)
            ->getQuery();

        $result = $qb->getSingleScalarResult();
        return $result ? new \DateTime($result) : null;
    }

    public function getNumberOfGamesForGivenWeek($weeknumber): int
    {
        return $this->createQueryBuilder('game')
            ->select('Count(game.id)')
            ->where('game.weekNumber =:weeknumber')
            ->setParameter('weeknumber', $weeknumber)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getWeeks(): array
    {
        $result = $this->createQueryBuilder('game')
            ->select('DISTINCT game.weekNumber')
            ->getQuery()
            ->getResult();

        return array_map('intval', array_column($result, 'weekNumber'));
    }

    public function isFinished(int $week): bool
    {
        $finishedGames = $this->createQueryBuilder('game')
            ->select('COUNT(game.id)')
            ->where('game.weekNumber = :week')
            ->andWhere('game.homeScore IS NOT NULL')
            ->andWhere('game.awayScore IS NOT NULL')
            ->setParameter('week', $week)
            ->getQuery()
            ->getSingleScalarResult();

        $numberOfGamesInWeek = $this->getNumberOfGamesForGivenWeek($week);
        if ($finishedGames === $numberOfGamesInWeek) {
            return true;
        }
        return false;
    }

    public function getPrimetimeGamesForWeek(int $week): array
    {
        $qb = $this->createQueryBuilder('g')
            ->where('HOUR(g.date) > 1 AND HOUR(g.date) < 4')
            ->andWhere('g.weekNumber = :week')
            ->setParameter('week', $week)
            ->getQuery();

        return $qb->getResult();
    }

    public function getSundayGamesForWeek(int $week): array
    {
        // because we use german time in the db, the primetime games are on monday morning
        return $this->createQueryBuilder('g')
            //the games played in europe start earlier so we have to I chose 12 as the starting point
            //Sunday night games are played on Monday morning in Europe between 1 and 4 (includes a small buffer)
            ->where('((HOUR(g.date) > 12 AND HOUR(g.date) < 24 AND DAYOFWEEK(g.date) = 1)
                    OR 
                    (HOUR(g.date) > 0 AND HOUR(g.date) < 4 AND DAYOFWEEK(g.date) = 2))')
            ->andWhere('g.weekNumber = :week')
            ->setParameter('week', $week)
            ->getQuery()
            ->getResult();
    }
}
