<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\BetRepository;
use App\Entity\Bet;
use App\Entity\User;
use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;


use function PHPUnit\Framework\assertEquals;

class BetRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $betRepository;
    private $mockedGameRepository;
    private $user;
    public $gameOne;
    public $gameTwo;
    public $betOne;
    public $betTwo;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->betRepository = $this->entityManager->getRepository(Bet::class);
        $this->mockedGameRepository = $this->createMock(GameRepository::class);
        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->gameOne = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => 1]);
        $this->gameTwo = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => 2]);

        // bet one will be used for the happy path tests
        $this->betOne = new Bet();
        $this->betOne->setUser($this->user);
        $this->betOne->setGame($this->gameOne);
        $this->betOne->setPoints(1);
        $this->betOne->setLastEdit($this->gameOne->getDate());
        $this->betOne->setEditCount(4);

        // bet two will be used for the unhappy path tests
        $this->betTwo = new Bet();
        $this->betTwo->setUser($this->user);
        $this->betTwo->setGame($this->gameTwo);
        $this->betTwo->setPoints(0);
        $this->betTwo->setLastEdit($this->gameTwo->getDate());
        $this->betTwo->setEditCount(3);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    //******* Two Minute Drill Tests *******
    //**************************************
    public function testFindTwoMinuteDrillHit()
    {
        $lastEdit = (clone $this->gameOne->getDate())->sub(new \DateInterval('PT1M'));
        $this->betOne->setLastEdit($lastEdit);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = count($this->betRepository->FindTwoMinuteDrillHit($this->user));
        assertEquals($result, 1);
    }

    public function testFindTwoMinuteDrillHitNoHits()
    {
        $lastEdit = (clone $this->gameOne->getDate())->add(new \DateInterval('PT1M'));
        $this->betOne->setLastEdit($lastEdit);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = count($this->betRepository->FindTwoMinuteDrillHit($this->user));
        assertEquals($result, 0);
    }

    //******* getCountOfHitsByUserForGivenWeek Tests *******
    //******************************************************
    public function testGetCountOfHitsByUserForGivenWeek()
    {

        // checking is the hit is detected
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();
        $hits = $this->betRepository->GetCountOfHitsByUserForGivenWeek($this->user, 1);
        assertEquals($hits, 1);

        // checking that adding another bet with no hit still returns 1
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();
        $hits = $this->betRepository->GetCountOfHitsByUserForGivenWeek($this->user, 1);
        assertEquals($hits, 1);
    }

    //******* findHitsByUser Tests *******
    //************************************
    public function testFindHitsByUser()
    {
        // Checking if the hit is detected
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();
        $hits = $this->betRepository->findHitsByUser($this->user);

        $this->assertCount(1, $hits);
        $this->assertEquals($this->betOne->getId(), $hits[0]->getId());

        // Checking that adding another bet with no hit still returns 1
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();
        // Clear entity manager to avoid persistence issues
        $this->entityManager->clear();

        $hits = $this->betRepository->findHitsByUser($this->user);
        $this->assertCount(1, $hits);
        $this->assertEquals($this->betOne->getId(), $hits[0]->getId());
    }

    //******* hasTrickPlayHit Tests *******
    //*************************************

    public function testHasTrickPlayHit()
    {

        // Checking if the trick play hit is detected
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();
        $result = $this->betRepository->hasTrickPlayHit($this->user);
        $this->assertTrue($result);

        // Checking that adding another bet with no trick play hit still returns true
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();
        $result = $this->betRepository->hasTrickPlayHit($this->user);
        $this->assertTrue($result);

        // Checking that removing the bet with the trick play hit returns false
        $this->entityManager->remove($this->betOne);
        $this->entityManager->flush();
        $result = $this->betRepository->hasTrickPlayHit($this->user);
        $this->assertFalse($result);

        // Checking that removing the bet with the no trick play hit still returns false
        $this->entityManager->remove($this->betTwo);
        $this->entityManager->flush();
        $result = $this->betRepository->hasTrickPlayHit($this->user);
        $this->assertFalse($result);
    }

    //******* hasPigskinProphetHit Tests *******
    //******************************************

    public function testPigskinProphetHit()
    {
        //detecting if the function correctly returns true
        $this->betTwo->setPoints(1);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->hasPigskinProphetHit($this->user);
        $this->assertTrue($result);

        //detecting if the function correctly returns false
        $this->betTwo->setPoints(0);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->hasPigskinProphetHit($this->user);
        $this->assertFalse($result);
    }

    //******* findBetsByWeeknumber Tests *******
    //******************************************
    public function testFindBetsByWeeknumber()
    {
        $this->betTwo->setPoints(1);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->findBetsByWeeknumber(1);
        $this->assertCount(2, $result);

        $result = $this->betRepository->findBetsByWeeknumber(2);
        $this->assertCount(0, $result);
    }

    //******* findNumberOfRegularSeasonBets Tests *******
    //***************************************************

    public function testFindNumberOfRegularSeasonBets()
    {
        $result = $this->betRepository->findNumberOfRegularSeasonBets($this->user);
        $this->assertEquals(0, $result);

        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();
        $result = $this->betRepository->findNumberOfRegularSeasonBets($this->user);
        $this->assertEquals(1, $result);

        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();
        $result = $this->betRepository->findNumberOfRegularSeasonBets($this->user);
        $this->assertEquals(2, $result);
    }

    //******* findLatestCompletedWeekNumber Tests *******
    //***************************************************
    public function testFindLatesCompletedWeek()
    {
        $result = $this->betRepository->findLatestCompletedWeekNumber($this->user);
        $this->assertEquals(0, $result);

        $this->entityManager->persist($this->betOne);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();
        $result = $this->betRepository->findLatestCompletedWeekNumber($this->user);
        $this->assertEquals(1, $result);
    }

    //******* getHighestScoringUser Tests *******
    //*******************************************
    public function testGetHighestScoringUser()
    {


        // testing if the function returns null when there are no bets
        $this->assertNull($this->betRepository->getHighestScoringUser(1));

        // testing if the function returns the user with the highest score
        $this->entityManager->persist($this->betOne);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->getHighestScoringUser(1);
        $this->assertEquals($this->user->getId(), $result->getId());

        //testing if the other user if there is a higher score
        $userTwo = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'testuser']);
        $betThree = new Bet();
        $betThree->setUser($userTwo);
        $betThree->setGame($this->gameOne);
        $betThree->setPoints(3);

        $this->entityManager->persist($betThree);
        $this->entityManager->flush();

        $result = $this->betRepository->getHighestScoringUser(1);
        $this->assertEquals($userTwo->getId(), $result->getId());
    }

    //******* hasUpsetHit Tests *******
    //*********************************
    public function testHasUpsetHit()
    {
        // testing if the function returns true when there is an upset hit
        $this->gameOne->setHomeScore(0);
        $this->gameOne->setHomeOdds(300);
        $this->gameOne->setAwayScore(1);
        $this->gameOne->setAwayOdds(-300);
        $this->betOne->setHomePrediction(0);
        $this->betOne->setAwayPrediction(1);
        $this->betOne->setPoints(1);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->hasUpsetHit($this->user);
        $this->assertTrue($result);

        // testing if the function returns false when there is no upset hit
        $this->gameOne->setHomeScore(1);
        $this->gameOne->setHomeOdds(300);
        $this->gameOne->setAwayScore(0);
        $this->gameOne->setAwayOdds(-300);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->flush();

        $result = $this->betRepository->hasUpsetHit($this->user);
        $this->assertFalse($result);
    }

    //******* hasPerfectlyBalancedHit Tests *******
    //*********************************************
    public function testHasPerfectlyBalancedHit()
    {
        // testing if the function returns true when there is a perfectly balanced hit
        $this->gameOne->setHomeScore(0);
        $this->gameOne->setAwayScore(0);
        $this->betOne->setHomePrediction(1);
        $this->betOne->setAwayPrediction(1);
        $this->betOne->setPoints(3);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->hasPerfectlyBalancedHit($this->user);
        $this->assertTrue($result);

        // testing if the function returns false when there is no perfectly balanced hit
        $this->gameOne->setHomeScore(1);
        $this->gameOne->setAwayScore(0);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->flush();

        $result = $this->betRepository->hasPerfectlyBalancedHit($this->user);
        $this->assertFalse($result);
    }

    //******* getTotalPointsByUserForWeek Tests *******
    //*************************************************
    public function testGetTotalPointsByUserForWeek()
    {
        // testing if the function returns the correct number of points
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->getTotalPointsByUserForWeek($this->user, 1);
        $this->assertEquals(1, $result);

        // testing if the function returns 0 when there are no points
        $this->betOne->setPoints(0);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();
        $result = $this->betRepository->getTotalPointsByUserForWeek($this->user, 1);
        $this->assertEquals(0, $result);

        // testing if the function returns 0 when there are no bets
        $result = $this->betRepository->getTotalPointsByUserForWeek($this->user, 2);
        $this->assertEquals(0, $result);

        //testing multiple bets
        $this->betTwo->setPoints(3);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();
        $result = $this->betRepository->getTotalPointsByUserForWeek($this->user, 1);
        $this->assertEquals(3, $result);

        $this->betOne->setPoints(5);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();
        $result = $this->betRepository->getTotalPointsByUserForWeek($this->user, 1);
        $this->assertEquals(8, $result);
    }

    //******* getWinnerThroughWeeks Tests *******
    //****************************************
    public function testGetWinnerThroughWeeks()
    {
        // testing if the function returns the correct winner
        $this->betOne->setPoints(3);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $userTwo = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'testuser']);
        $testUserBet = new Bet();
        $testUserBet->setUser($userTwo);
        $testUserBet->setPoints(0);
        $testUserBet->setGame($this->gameOne);
        $this->entityManager->persist($testUserBet);
        $this->entityManager->flush();

        $result = $this->betRepository->getWinnerThroughWeeks(1, 6);
        $this->assertEquals($this->user->getId(), $result->getId());

        // now test changing the winner
        $testUserBet->setPoints(5);
        $this->entityManager->persist($testUserBet);
        $this->entityManager->flush();

        $result = $this->betRepository->getWinnerThroughWeeks(1, 6);
        $this->assertEquals($userTwo->getId(), $result->getId());

        // add a bet in week 6 for the first user
        $gameWeekSix = new Game();
        $gameWeekSix->setWeekNumber(6);
        $gameWeekSix->setDate(new \DateTime('2021-09-12 13:00:00'));
        $gameWeekSix->setLocation('heaven');

        $betWeekSix = new Bet();
        $betWeekSix->setUser($this->user);
        $betWeekSix->setGame($gameWeekSix);
        $betWeekSix->setPoints(5);
        $this->entityManager->persist($gameWeekSix);
        $this->entityManager->persist($betWeekSix);
        $this->entityManager->flush();

        // test that the function still returns the correct winner
        $result = $this->betRepository->getWinnerThroughWeeks(1, 6);
        $this->assertEquals($this->user->getId(), $result->getId());

        // add a game in week 7 for user 2 and check if the result stays the sam
        $gameWeekSeven = new Game();
        $gameWeekSeven->setWeekNumber(7);
        $gameWeekSeven->setDate(new \DateTime('2021-09-12 13:00:00'));
        $gameWeekSeven->setLocation('heaven');

        $betWeekSeven = new Bet();
        $betWeekSeven->setUser($userTwo);
        $betWeekSeven->setGame($gameWeekSeven);
        $betWeekSeven->setPoints(5);
        $this->entityManager->persist($gameWeekSeven);
        $this->entityManager->persist($betWeekSeven);
        $this->entityManager->flush();

        $result = $this->betRepository->getWinnerThroughWeeks(1, 6);
        $this->assertEquals($this->user->getId(), $result->getId());
    }

    //******* getTotalPointsByUserForAllWeeks Tests *******
    //*****************************************************

    public function testGetTotalPointsByUserForAllWeeks()
    {
        $weeks = range(1, 3);
        $this->mockedGameRepository->method('getWeeks')->willReturn($weeks);

        // Assume bets only for week 1
        $this->betOne->setPoints(3);
        $this->betTwo->setPoints(5);
        $this->betOne->getGame()->setWeekNumber(1);
        $this->betTwo->getGame()->setWeekNumber(1);

        // No bets for weeks 2 and 3
        $gameWeekTwo = new Game();
        $gameWeekTwo->setWeekNumber(2);
        $gameWeekTwo->setDate(new \DateTime('2021-09-12 13:00:00'));
        $gameWeekTwo->setLocation('heaven');

        $gameWeekThree = new Game();
        $gameWeekThree->setWeekNumber(3);
        $gameWeekThree->setDate(new \DateTime('2021-09-12 13:00:00'));
        $gameWeekThree->setLocation('heaven');

        $this->entityManager->persist($gameWeekTwo);
        $this->entityManager->persist($gameWeekThree);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $expectedPoints = [
            1 => 8,  // Total points for week 1
            2 => 0,  // No bets for week 2
            3 => 0   // No bets for week 3
        ];

        $result = $this->betRepository->getTotalPointsByUserForAllWeeks($this->user);

        $this->assertEquals($expectedPoints, $result);
    }

    //******* hasUnderdogLoverHit Tests *******
    //*****************************************
    public function testHasUnderdogLoverHit()
    {
        $this->gameOne->setHomeScore(0);
        $this->gameOne->setHomeOdds(300);
        $this->gameOne->setAwayScore(1);
        $this->gameOne->setAwayOdds(-300);
        $this->betOne->setHomePrediction(0);
        $this->betOne->setAwayPrediction(1);
        $this->betOne->setPoints(5);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->hasUnderdogLoverHit($this->user);
        $this->assertTrue($result);
    }

    public function testHasNoUnderdogLoverHit()
    {
        $this->gameOne->setHomeScore(0);
        $this->gameOne->setHomeOdds(-300);
        $this->gameOne->setAwayScore(1);
        $this->gameOne->setAwayOdds(300);
        $this->betOne->setHomePrediction(0);
        $this->betOne->setAwayPrediction(1);
        $this->betOne->setPoints(5);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->hasUnderdogLoverHit($this->user);
        $this->assertFalse($result);
    }

    //******* getNailbiterHitCount Tests *******
    //******************************************
    public function testGetNailBiterHitCount()
    {
        $this->gameOne->setHomeScore(1);
        $this->gameOne->setAwayScore(0);
        $this->betOne->setPoints(5);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->getNailbiterHitCount($this->user);
        $this->assertEquals(1, $result);

        $this->gameTwo->setHomeScore(1);
        $this->gameTwo->setAwayScore(0);
        $this->betTwo->setPoints(5);
        $this->entityManager->persist($this->gameTwo);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->getNailbiterHitCount($this->user);
        $this->assertEquals(2, $result);
    }

    //******* getSweepHitCount Tests *******
    //**************************************
    public function testGetSweepHitCount()
    {
        $this->gameOne->setHomeScore(21);
        $this->gameOne->setAwayScore(0);
        $this->betOne->setPoints(5);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->getSweepHitCount($this->user);
        $this->assertEquals(1, $result);

        $this->gameTwo->setHomeScore(21);
        $this->gameTwo->setAwayScore(0);
        $this->betTwo->setPoints(5);
        $this->entityManager->persist($this->gameTwo);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->getSweepHitCount($this->user);
        $this->assertEquals(2, $result);

        $this->gameTwo->setAwayScore(1);
        $this->entityManager->persist($this->gameTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->getSweepHitCount($this->user);
        $this->assertEquals(1, $result);
    }

    //******* getPrimetimeBets Tests *******
    //***************************************
    public function testGetPrimetimeBets()
    {
        $this->gameOne->setDate(new \DateTime('2021-09-12 13:00:00'));
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->getPrimetimeBetsForWeek($this->user, 1);
        $this->assertCount(0, $result);

        $this->gameTwo->setDate(new \DateTime('2021-09-12 02:00:00'));
        $this->entityManager->persist($this->gameTwo);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->getPrimetimeBetsForWeek($this->user, 1);
        $this->assertCount(1, $result);

        $this->gameOne->setDate(new \DateTime('2021-09-12 03:00:00'));
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->flush();

        $result = $this->betRepository->getPrimetimeBetsForWeek($this->user, 1);
        $this->assertCount(2, $result);

        $this->gameOne->setWeekNumber(2);
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->flush();

        $result = $this->betRepository->getPrimetimeBetsForWeek($this->user, 1);
        $this->assertCount(1, $result);
    }

    //******* getSundayBets Tests *******
    //***********************************
    public function testGetSundayBets()
    {
        // early sunday game
        $this->gameOne->setDate(new \DateTime('2024-09-08 15:00:00'));
        $this->entityManager->persist($this->gameOne);
        $this->entityManager->persist($this->betOne);
        $this->entityManager->flush();

        $result = $this->betRepository->getSundayBetsForWeek($this->user, 1);
        $this->assertCount(1, $result);

        // saturday night game, should not be included
        $this->gameTwo->setDate(new \DateTime('2024-09-08 02:00:00'));
        $this->entityManager->persist($this->gameTwo);
        $this->entityManager->persist($this->betTwo);
        $this->entityManager->flush();

        $result = $this->betRepository->getSundayBetsForWeek($this->user, 1);
        $this->assertCount(1, $result);
    }
}
