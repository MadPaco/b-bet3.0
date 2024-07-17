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
    public function hasUpsetHit(User $user): bool;
    public function hasPerfectlyBalancedHit(User $user): bool;
    
}
