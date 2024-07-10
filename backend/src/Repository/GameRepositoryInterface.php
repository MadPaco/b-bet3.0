<?php

namespace App\Repository;

use Doctrine\Persistence\ObjectRepository;

interface GameRepositoryInterface extends ObjectRepository
{
    public function getEarliestGameDate(int $weekNumber): \DateTime;
}
