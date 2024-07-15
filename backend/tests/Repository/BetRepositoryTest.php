<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Bet;
use App\Entity\User;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;


use function PHPUnit\Framework\assertEquals;

class BetRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $betRepository;
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
}
