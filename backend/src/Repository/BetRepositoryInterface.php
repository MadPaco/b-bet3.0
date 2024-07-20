<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Bet;

interface BetRepositoryInterface
{
    public function findHitsByUser(User $user);
    public function findTwoMinuteDrillHit(User $user);
    public function findBetsByWeeknumber($weekNumber);
    public function findNumberOfRegularSeasonBets(User $user): int;
    public function getCountOfHitsByUserForGivenWeek(User $user, int $week): int;
    public function findLatestCompletedWeekNumber(User $user): int;
    public function hasTrickPlayHit(User $user): bool;
    public function hasPigskinProphetHit(User $user): bool;
    public function getHighestScoringUser(int $week): ?User;
    public function getCountOfAllHitsByUser(User $user): int;
    public function getTotalPointsByUser(User $user): int;
    public function getTotalPointsByUserForWeek(User $user, int $week): int;
    public function getTotalPointsByUserForAllWeeks(User $user): array;
    public function hasUpsetHit(User $user): bool;
    public function hasPerfectlyBalancedHit(User $user): bool;
    public function getWinnerThroughWeeks(int $begin, int $end): ?User;
    public function hasUnderdogLoverHit(User $user): bool;
    public function getNailbiterHitCount(User $user): int;
    public function getSweepHitCount(User $user): int;
}
