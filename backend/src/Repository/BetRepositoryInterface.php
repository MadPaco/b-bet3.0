<?php

namespace App\Repository;

use App\Entity\User;

interface BetRepositoryInterface
{
    public function findHitsByUser(User $user);
    public function findTwoMinuteDrillHit(User $user);
    public function findBetsByWeeknumber($weekNumber);
    public function findNumberOfRegularSeasonBets(User $user): int;
    public function findLatestCompletedWeekNumber(User $user): int;
}
