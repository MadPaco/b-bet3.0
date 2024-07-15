<?php

namespace App\Repository;

use Doctrine\Persistence\ObjectRepository;
use App\Entity\NflTeam;

interface GameRepositoryInterface extends ObjectRepository
{
    public function getEarliestGameDate(int $weekNumber): \DateTime;
    public function getWeeks(): array;
    public function getNumberOfGamesForGivenWeek($weeknumber): int;
    public function getWinsOfTeam(NflTeam $team): int;
    public function findLatestWeekWithResults(): int;
    public function findFinishedGames(): array;
    public function findDivisionGamesForTeam(NflTeam $team): array;
    public function findGamesWithSameDivisionAndConference(string $conference, string $division): array;
}
