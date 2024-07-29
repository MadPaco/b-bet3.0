<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Bet;
use App\Entity\NflTeam;

interface BetRepositoryInterface
{
    //find bets
    public function findBetsByWeeknumber($weekNumber): array;
    public function findBetsByUserForWeek(User $user, int $week): array;
    public function findNumberOfRegularSeasonBets(User $user): int;
    public function getPrimetimeBetsForWeek(User $user, int $week): array;
    public function getSundayBetsForWeek(User $user, int $week): array;
    public function getThursdayNightBet(User $user, int $week): ?Bet;

    //find hits
    public function findHitsByUser(User $user);
    public function findTwoMinuteDrillHit(User $user);
    public function getCountOfHitsByUserForGivenWeek(User $user, int $week): int;
    public function hasTrickPlayHit(User $user): bool;
    public function hasPigskinProphetHit(User $user): bool;
    public function getCountOfAllHitsByUser(User $user): int;
    public function hasUpsetHit(User $user): bool;
    public function hasPerfectlyBalancedHit(User $user): bool;
    public function hasUnderdogLoverHit(User $user): bool;
    public function getNailbiterHitCount(User $user): int;
    public function getSweepHitCount(User $user): int;
    public function getTeamHits(User $user, NflTeam $team): int;

    //points related
    public function getHighestScoringUser(int $week): ?User;
    public function getTotalPointsByUser(User $user): int;
    public function getTotalPointsByUserForWeek(User $user, int $week): int;
    public function getTotalPointsByUserForAllWeeks(User $user): array;
    public function getWinnerThroughWeeks(int $begin, int $end): ?User;

    //misc
    //returns the latest week where the user placed all bets
    public function findLatestCompletedWeekNumber(User $user): int;
}
