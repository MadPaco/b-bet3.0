<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

class GameRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $gameRepository;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->gameRepository = $this->entityManager->getRepository(Game::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    //******* getWeeks Tests *******
    //******************************
    public function testGetWeeks()
    {
        // Create new Game entities for each week number
        $game1 = new Game();
        $game1->setWeekNumber(1);
        $game1->setDate(new \DateTime('2021-09-09 20:20:00'));
        $game1->setLocation('heaven');
        $this->entityManager->persist($game1);

        $game2 = new Game();
        $game2->setWeekNumber(2);
        $game2->setDate(new \DateTime('2021-09-16 20:20:00'));
        $game2->setLocation('heaven');
        $this->entityManager->persist($game2);

        $game3 = new Game();
        $game3->setWeekNumber(3);
        $game3->setDate(new \DateTime('2021-09-23 20:20:00'));
        $game3->setLocation('heaven');
        $this->entityManager->persist($game3);
        $this->entityManager->flush();

        // Call getWeeks() to retrieve the week numbers
        $weeks = $this->gameRepository->getWeeks();

        // Check if the number of weeks retrieved matches the number of weeks added
        assertEquals(3, count($weeks));
    }

    //******* getNumberOfGamesForGivenWeek Tests *******
    //**************************************************
    public function testGetNumberOfGamesForGivenWeek()
    {
        // in the testDB we have 2 games for week 1
        $numberOfGames = $this->gameRepository->getNumberOfGamesForGivenWeek(1);
        assertEquals(2, $numberOfGames);

        // add a game in week 1
        $game = new Game();
        $game->setWeekNumber(1);
        $game->setDate(new \DateTime('2021-09-09 20:20:00'));
        $game->setLocation('heaven');
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        // check if the number of games for week 1 has increased
        $numberOfGames = $this->gameRepository->getNumberOfGamesForGivenWeek(1);
        assertEquals(3, $numberOfGames);

        // add a game in week 2 and check if the number of games for week 1 is still the same
        $game = new Game();
        $game->setWeekNumber(2);
        $game->setDate(new \DateTime('2021-09-16 20:20:00'));
        $game->setLocation('heaven');
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $numberOfGames = $this->gameRepository->getNumberOfGamesForGivenWeek(1);
        assertEquals(3, $numberOfGames);
    }

    //******* getEarliestGameDate Tests *******
    //*****************************************
    public function testGetEarliestGameDate()
    {
        // add a very early game in week 1
        $game = new Game();
        $game->setWeekNumber(1);
        $game->setDate(new \DateTime('1993-08-22 20:20:00'));
        $game->setLocation('heaven');
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        // check if the earliest game date is the same as the one we added
        $earliestGameDate = $this->gameRepository->getEarliestGameDate(1);
        assertEquals('1993-08-22 20:20:00', $earliestGameDate->format('Y-m-d H:i:s'));
    }

    //******* testGetWinsOfTeam Tests *******
    //***************************************
    public function testGetWinsOfTeam()
    {
        $game = $this->gameRepository->findOneBy(['id' => 1]);
        $game->setHomeScore(1);
        $game->setAwayScore(0);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $team = $game->getHomeTeam();
        $numberOfWins = $this->gameRepository->getWinsOfTeam($team);
        // assertEquals with the number of wins directly
        assertEquals(1, $numberOfWins);

        // remove the win and check if the number of wins is 0
        $game->setHomeScore(0);
        $game->setAwayScore(1);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $numberOfWins = $this->gameRepository->getWinsOfTeam($team);
        // assertEquals with the number of wins directly
        assertEquals(0, $numberOfWins);

        // add a win in week 2 and check if the number of wins is 1
        $game = new Game();
        $game->setWeekNumber(2);
        $game->setDate(new \DateTime('2021-09-16 20:20:00'));
        $game->setLocation('heaven');
        $game->setHomeTeam($team);
        $game->setHomeScore(1);
        $game->setAwayScore(0);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $numberOfWins = $this->gameRepository->getWinsOfTeam($team);
        assertEquals(1, $numberOfWins);
    }

    //******* findLatestWeekWithResults Tests *******
    //***********************************************
    public function testFindLatestWeekWithResults()
    {
        // the db has 2 games without scores for week 1
        assertEquals($this->gameRepository->findLatestWeekWithResults(), 0);

        // add scores to the games in week 1
        $game = $this->gameRepository->findOneBy(['id' => 1]);
        $game->setHomeScore(1);
        $game->setAwayScore(0);
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        assertEquals(1, $this->gameRepository->findLatestWeekWithResults());

        // add scores to week 22 and check if the latest week with results is 22
        $game = new Game();
        $game->setWeekNumber(22);
        $game->setDate(new \DateTime('2021-09-16 20:20:00'));
        $game->setLocation('heaven');
        $game->setHomeScore(1);
        $game->setAwayScore(0);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        assertEquals(22, $this->gameRepository->findLatestWeekWithResults());
    }

    //******* isFinished Tests *******
    //********************************
    public function testIsFinished()
    {
        assertFalse($this->gameRepository->isFinished(1));
        $gameOne = $this->gameRepository->findOneBy(['id' => 1]);
        $gameTwo = $this->gameRepository->findOneBy(['id' => 2]);

        $gameOne->setHomeScore(1);
        $gameOne->setAwayScore(0);

        $gameTwo->setHomeScore(1);
        $gameTwo->setAwayScore(0);

        $this->entityManager->persist($gameOne);

        assertFalse($this->gameRepository->isFinished(1));

        $this->entityManager->persist($gameTwo);
        $this->entityManager->flush();

        assertTrue($this->gameRepository->isFinished(1));
    }

    // to do: findFinishedGames, findDivisionGamesForTeam, findGamesWithSameDivisionAndConference
}
