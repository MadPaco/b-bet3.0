<?php

namespace App\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\ResultsAchievementChecker;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\AchievementRepository;
use App\Repository\UserAchievementRepository;
use App\Repository\BetRepositoryInterface;
use App\Repository\UserRepository;
use App\Entity\Bet;
use App\Entity\Game;
use App\Entity\Achievement;
use App\Repository\GameRepositoryInterface;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;


class ResultsAchievementCheckerTest extends WebTestCase
{
    private $achievementChecker;
    private $entityManager;
    private $reflectedChecker;
    private $user;
    private $achievementRepository;
    private $userAchievementRepository;
    private $betRepository;
    private $userRepository;
    private $gameRepository;

    protected function setUp(): void
    {
        parent::setUp();
        date_default_timezone_set('Europe/Berlin');
        // Boot the kernel to get the container
        self::bootKernel();

        // Fetch the required services from the container
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->achievementRepository = self::getContainer()->get(AchievementRepository::class);
        $this->userAchievementRepository = self::getContainer()->get(UserAchievementRepository::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);

        // create mocks
        $this->betRepository = $this->createMock(BetRepositoryInterface::class);
        $this->gameRepository = $this->createMock(GameRepositoryInterface::class);
        // Instantiate the service with the dependencies
        $this->achievementChecker = new ResultsAchievementChecker($this->entityManager, $this->betRepository, $this->achievementRepository, $this->gameRepository);

        //reflect the checker to make the functions accessible
        $this->reflectedChecker = new ReflectionClass($this->achievementChecker);

        //get a user to run the tests on
        $this->user = $this->userRepository->findOneBy(['username' => 'admin']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    // helper functions

    private function setUpAchievement($achievementName): Achievement
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => $achievementName]);
        assertNotNull($achievement);
        return $achievement;
    }

    private function setUpReflection($methodName): \ReflectionMethod
    {
        $method = $this->reflectedChecker->getMethod($methodName);
        assertNotNull($method);
        $method->setAccessible(true);
        return $method;
    }

    private function assertUserAchievement($user, $achievement, bool $shouldExist): void
    {
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $user,
                'achievement' => $achievement
            ]
        );
        if ($shouldExist) {
            assertNotNull($userAchievement);
        } else {
            assertNull($userAchievement);
        }
    }

    // used for consistency is key test
    private function generateCalls(): array
    {
        $calls = [];
        for ($i = 1; $i <= 22; $i++) {
            $calls[] = [$this->user, $i];
        }
        return $calls;
    }

    public function generateReturns($amountOfHits): array
    {
        $returns = [];
        for ($i = 1; $i <= $amountOfHits; $i++) {
            $returns[] = 1;
        }
        for ($i = $amountOfHits + 1; $i <= 22; $i++) {
            $returns[] = 0;
        }
        return $returns;
    }

    public function setUpGame($week): Game
    {
        $game = new Game;
        $game->setWeekNumber($week);
        $game->setLocation('heaven');
        $game->setDate(new \DateTime('2021-09-09 20:00:00'));
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }

    public function setUpBet($user, $game, $points): Bet
    {
        $bet = new Bet;
        $bet->setUser($user);
        $bet->setGame($game);
        $bet->setPoints($points);
        $this->entityManager->persist($bet);
        $this->entityManager->flush();

        return $bet;
    }

    // tests

    public function testCheckFirstDown(): void
    {
        $achievement = $this->setUpAchievement('First Down');
        $checkFirstDownReflection = $this->setUpReflection('checkFirstDown');
        //this will return a hit meaning that the achievement should be awarded 
        //after executing checkFirstDown
        $bet = new Bet;
        $this->betRepository->method('findHitsByUser')->with($this->user)->willReturn([$bet]);

        //check that the user doesn"t have the achievement yet
        $this->assertUserAchievement($this->user, $achievement, false);

        //invoke the check
        $checkFirstDownReflection->invoke($this->achievementChecker, $this->user);

        //check that the achievement has beeen awarded
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckTwoMinuteDrill(): void
    {
        $achievement = $this->setUpAchievement('Two-Minute Drill');

        $checkTwoMinuteDrillReflection = $this->setUpReflection('checkTwoMinuteDrill');

        //check that the user doesn"t have the achievement yet
        $this->assertUserAchievement($this->user, $achievement, false);

        $bet = new Bet;
        $this->betRepository->method('findTwoMinuteDrillHit')->with($this->user)->willReturn([$bet]);
        $checkTwoMinuteDrillReflection->invoke($this->achievementChecker, $this->user);

        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckTrickPlay(): void
    {
        $achievement = $this->setUpAchievement('Trick Play');

        $checkTrickPlayReflection = $this->setUpReflection('checkTrickPlay');

        //check that the user doesn"t have the achievement yet
        $this->assertUserAchievement($this->user, $achievement, false);

        $this->betRepository->method('hasTrickPlayHit')->with($this->user)->willReturn(true);
        $checkTrickPlayReflection->invoke($this->achievementChecker, $this->user);

        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckPigskinProphet(): void
    {
        $achievement = $this->setUpAchievement('Pigskin Prophet');
        $checkPigskinProphetReflection = $this->setUpReflection('checkPigskinProphet');

        // there are 2 games in the db for week 1, but no scores
        // check that the user doesn"t have the achievement yet
        $this->assertUserAchievement($this->user, $achievement, false);

        //set the user to have a hit in the pigskin prophet
        $this->betRepository->method('hasPigskinProphetHit')->with($this->user)->willReturn(true);

        //invoke the check
        $checkPigskinProphetReflection->invoke($this->achievementChecker, $this->user);

        //check that the achievement has been awarded
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckTouchdown(): void
    {
        $checkTouchdownReflection = $this->setUpReflection('checkTouchdown');

        $achievement = $this->setUpAchievement('Touchdown');

        $this->assertUserAchievement($this->user, $achievement, false);

        $bet = new Bet;
        $bet->setPoints(7);
        $this->betRepository->method('findHitsByUser')->with($this->user)->willReturn([$bet]);

        $checkTouchdownReflection->invoke($this->achievementChecker, $this->user);
        //check that the achievement has been awarded

        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testPickSix(): void
    {
        $checkPickSixReflection = $this->setUpReflection('checkPickSix');
        $achievement = $this->setUpAchievement('Pick Six');
        $this->assertUserAchievement($this->user, $achievement, false);

        $this->gameRepository->method('findLatestWeekWithResults')->willReturn(1);
        $this->betRepository->method('getCountOfHitsByUserForGivenWeek')->with($this->user, 1)->willReturn(6);
        $checkPickSixReflection->invoke($this->achievementChecker, $this->user);

        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testPickSixNoResults(): void
    {
        $checkPickSixReflection = $this->setUpReflection('checkPickSix');

        $achievement = $this->setUpAchievement('Pick Six');

        // if findLatestWeekWithResults return 0 there are not results set yet
        $this->gameRepository->method('findLatestWeekWithResults')->willReturn(0);
        $this->betRepository->method('getCountOfHitsByUserForGivenWeek')->with($this->user, 0)->willReturn(6);

        $checkPickSixReflection->invoke($this->achievementChecker, $this->user);

        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckMVPOfTheWeek(): void
    {
        $checkMVPOfTheWeekReflection = $this->setUpReflection('checkMVPOfTheWeek');

        $achievement = $this->setUpAchievement('MVP of the Week');

        $this->gameRepository->method('findLatestWeekWithResults')->willReturn(1);
        $this->gameRepository->method('isFinished')->with(1)->willReturn(true);
        $this->betRepository->method('getHighestScoringUser')->with(1)->willReturn($this->user);

        $checkMVPOfTheWeekReflection->invoke($this->achievementChecker);
        //check that the achievement has been awarded
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckBowlGameSecured(): void
    {
        $checkBowlGameReflection = $this->setUpReflection('checkBowlGameSecured');
        $achievement = $this->setUpAchievement('Bowl Game Secured');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getCountOfAllHitsByUser')->with($this->user)->willReturn(150);
        $checkBowlGameReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckBowlGameSecuredNotEnoughHits(): void
    {
        $checkBowlGameReflection = $this->setUpReflection('checkBowlGameSecured');
        $achievement = $this->setUpAchievement('Bowl Game Secured');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getCountOfAllHitsByUser')->with($this->user)->willReturn(135);
        $checkBowlGameReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckProBowler(): void
    {
        $checkProBowlerReflection = $this->setUpReflection('checkProBowler');
        $achievement = $this->setUpAchievement('Pro Bowler');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getTotalPointsByUser')->with($this->user)->willReturn(100);
        $checkProBowlerReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckProBowlerNotEnoughPoints(): void
    {
        $checkProBowlerReflection = $this->setUpReflection('checkProBowler');
        $achievement = $this->setUpAchievement('Pro Bowler');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getTotalPointsByUser')->with($this->user)->willReturn(99);
        $checkProBowlerReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckAllPro(): void
    {
        $checkAllProReflection = $this->setUpReflection('checkAllPro');
        $achievement = $this->setUpAchievement('All Pro');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getTotalPointsByUser')->with($this->user)->willReturn(200);
        $checkAllProReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckAllProNotEnoughPoints(): void
    {
        $checkAllProReflection = $this->setUpReflection('checkAllPro');
        $achievement = $this->setUpAchievement('All Pro');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getTotalPointsByUser')->with($this->user)->willReturn(199);
        $checkAllProReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckUpsetSpecialist(): void
    {
        $checkUpsetSpecialistReflection = $this->setUpReflection('checkUpsetSpecialist');
        $achievement = $this->setUpAchievement('Upset Specialist');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('hasUpsetHit')->with($this->user)->willReturn(true);
        $checkUpsetSpecialistReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckPerfectlyBalanced(): void
    {
        $checkPerfectlyBalancedReflection = $this->setUpReflection('checkPerfectlyBalanced');
        $achievement = $this->setUpAchievement('Perfectly Balanced');

        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('hasPerfectlyBalancedHit')->with($this->user)->willReturn(true);
        $checkPerfectlyBalancedReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckConsistencyIsKey(): void
    {
        $checkConsistencyIsKeyReflection = $this->setUpReflection('checkConsistencyIsKey');
        $achievement = $this->setUpAchievement('Consistency is Key');
        $callValues = $this->generateCalls();
        $returnValues = $this->generateReturns(10);

        $this->gameRepository->method('getLatestFinishedWeek')->willReturn(10);
        // Mock getTotalPointsByUserForWeek for 10 different weeks
        $this->betRepository->method('getTotalPointsByUserForWeek')
            ->withConsecutive(...$callValues)
            ->willReturnOnConsecutiveCalls(...$returnValues);

        // Check that the user doesn't have the achievement yet
        $this->assertUserAchievement($this->user, $achievement, false);

        // Invoke the checkConsistencyIsKey method
        $checkConsistencyIsKeyReflection->invoke($this->achievementChecker, $this->user);

        // Check that the achievement has been awarded
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckConsistencyIsKeyNotEnoughHits(): void
    {
        $checkConsistencyIsKeyReflection = $this->setUpReflection('checkConsistencyIsKey');
        $achievement = $this->setUpAchievement('Consistency is Key');
        $callValues = $this->generateCalls();
        $returnValues = $this->generateReturns(9);

        // Mock getTotalPointsByUserForWeek for 10 different weeks
        $this->betRepository->method('getTotalPointsByUserForWeek')
            ->withConsecutive(...$callValues)
            ->willReturnOnConsecutiveCalls(...$returnValues);

        // Check that the user doesn't have the achievement yet
        $this->assertUserAchievement($this->user, $achievement, false);

        // Invoke the checkConsistencyIsKey method
        $checkConsistencyIsKeyReflection->invoke($this->achievementChecker, $this->user);

        // Check that the achievement has not been awarded
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckConsistencyIsKeyWeekNotFinished(): void
    {
        $checkConsistencyIsKeyReflection = $this->setUpReflection('checkConsistencyIsKey');
        $achievement = $this->setUpAchievement('Consistency is Key');
        $callValues = $this->generateCalls();
        $returnValues = $this->generateReturns(10);

        $this->gameRepository->method('getLatestFinishedWeek')->willReturn(9);
        // Mock getTotalPointsByUserForWeek for 10 different weeks
        $this->betRepository->method('getTotalPointsByUserForWeek')
            ->withConsecutive(...$callValues)
            ->willReturnOnConsecutiveCalls(...$returnValues);

        // Check that the user doesn't have the achievement yet
        $this->assertUserAchievement($this->user, $achievement, false);

        // Invoke the checkConsistencyIsKey method
        $checkConsistencyIsKeyReflection->invoke($this->achievementChecker, $this->user);

        // Check that the achievement has been awarded
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckHeadstart(): void
    {
        $checkHeadstartReflection = $this->setUpReflection('checkHeadstart');
        $achievement = $this->setUpAchievement('Headstart');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getWinnerThroughWeeks')->with(1, 6)->willReturn($this->user);
        $this->gameRepository->method('isFinished')->with(6)->willReturn(true);
        $checkHeadstartReflection->invoke($this->achievementChecker);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckHeadstartWeekSixNotFinished(): void
    {
        $checkHeadstartReflection = $this->setUpReflection('checkHeadstart');
        $achievement = $this->setUpAchievement('Headstart');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getWinnerThroughWeeks')->with(1, 6)->willReturn($this->user);
        $this->gameRepository->method('isFinished')->with(6)->willReturn(false);
        $checkHeadstartReflection->invoke($this->achievementChecker);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testMidseasonForm(): void
    {
        $checkMidseasonFormReflection = $this->setUpReflection('checkMidseasonForm');
        $achievement = $this->setUpAchievement('Midseason Form');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getWinnerThroughWeeks')->with(7, 12)->willReturn($this->user);
        $this->gameRepository->method('isFinished')->with(12)->willReturn(true);
        $checkMidseasonFormReflection->invoke($this->achievementChecker);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckMidseasonFormWeekTwelveNotFinished(): void
    {
        $checkMidseasonFormReflection = $this->setUpReflection('checkMidseasonForm');
        $achievement = $this->setUpAchievement('Midseason Form');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getWinnerThroughWeeks')->with(7, 12)->willReturn($this->user);
        $this->gameRepository->method('isFinished')->with(12)->willReturn(false);
        $checkMidseasonFormReflection->invoke($this->achievementChecker);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckPlayoffPush(): void
    {
        $checkPlayoffPushReflection = $this->setUpReflection('checkPlayoffPush');
        $achievement = $this->setUpAchievement('Playoff Push');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getWinnerThroughWeeks')->with(13, 18)->willReturn($this->user);
        $this->gameRepository->method('isFinished')->with(18)->willReturn(true);
        $checkPlayoffPushReflection->invoke($this->achievementChecker);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckPlayoffPushWeekEighteenNotFinished(): void
    {
        $checkPlayoffPushReflection = $this->setUpReflection('checkPlayoffPush');
        $achievement = $this->setUpAchievement('Playoff Push');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getWinnerThroughWeeks')->with(13, 18)->willReturn($this->user);
        $this->gameRepository->method('isFinished')->with(18)->willReturn(false);
        $checkPlayoffPushReflection->invoke($this->achievementChecker);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckConsecutivePoints(): void
    {
        //Inputs: User $user, int $minPoints, int $minStreak, bool $lessThan = false
        $checkConsecutivePointsReflection = $this->setUpReflection('checkConsecutivePoints');
        $this->betRepository->method('getTotalPointsByUserForAllWeeks')->with($this->user)->willReturn([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]);

        // check a streak of 1 game with 3 or more points
        $result = $checkConsecutivePointsReflection->invoke($this->achievementChecker, $this->user, 1, 18, false);
        $this->assertTrue($result);

        // check for a streak of 18 games with less than 1 points
        $result = $checkConsecutivePointsReflection->invoke($this->achievementChecker, $this->user, 1, 18, true);
        $this->assertFalse($result);

        // check for a streak of 5 games with 5 or more points
        $result = $checkConsecutivePointsReflection->invoke($this->achievementChecker, $this->user, 5, 5, false);
        $this->assertTrue($result);

        // check for a streak of 5 games with less than 5 points
        $result = $checkConsecutivePointsReflection->invoke($this->achievementChecker, $this->user, 5, 5, true);
        $this->assertFalse($result);
    }

    public function testCheckByeWeek()
    {
        $checkByeWeekReflection = $this->setUpReflection('checkByeWeek');
        $achievement = $this->setUpAchievement('Bye Week');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getTotalPointsByUserForAllWeeks')->with($this->user)->willReturn([1 => 0]);
        $this->gameRepository->method('isFinished')->with(1)->willReturn(true);
        $checkByeWeekReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckByeWeekNotFinished()
    {
        $checkByeWeekReflection = $this->setUpReflection('checkByeWeek');
        $achievement = $this->setUpAchievement('Bye Week');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getTotalPointsByUserForAllWeeks')->with($this->user)->willReturn([1 => 0]);
        $this->gameRepository->method('isFinished')->with(1)->willReturn(false);
        $checkByeWeekReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckByeWeekNoZeroWeek()
    {
        $checkByeWeekReflection = $this->setUpReflection('checkByeWeek');
        $achievement = $this->setUpAchievement('Bye Week');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getTotalPointsByUserForAllWeeks')->with($this->user)->willReturn([1 => 1, 2 => 1]);
        $this->gameRepository->method('isFinished')->with(1)->willReturn(true);
        $checkByeWeekReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckNailBiter()
    {
        $checkNailBiterReflection = $this->setUpReflection('checkNailBiter');
        $achievement = $this->setUpAchievement('Nail-Biter');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getNailbiterHitCount')->with($this->user)->willReturn(5);
        $checkNailBiterReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckNailBiterNotEnougHits()
    {
        $checkNailBiterReflection = $this->setUpReflection('checkNailBiter');
        $achievement = $this->setUpAchievement('Nail-Biter');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getNailbiterHitCount')->with($this->user)->willReturn(4);
        $checkNailBiterReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckSweep()
    {
        $checkBlowoutBossReflection = $this->setUpReflection('checkSweep');
        $achievement = $this->setUpAchievement('Sweep');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getSweepHitCount')->with($this->user)->willReturn(5);
        $checkBlowoutBossReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckSweepNotEnoughHits()
    {
        $checkBlowoutBossReflection = $this->setUpReflection('checkSweep');
        $achievement = $this->setUpAchievement('Sweep');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('getSweepHitCount')->with($this->user)->willReturn(4);
        $checkBlowoutBossReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckUnderdogLover()
    {
        $checkUnderdogLoverReflection = $this->setUpReflection('checkUnderdogLover');
        $achievement = $this->setUpAchievement('Underdog Lover');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('hasUnderdogLoverHit')->with($this->user)->willReturn(true);
        $checkUnderdogLoverReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckUnderdogLoverNoHit()
    {
        $checkUnderdogLoverReflection = $this->setUpReflection('checkUnderdogLover');
        $achievement = $this->setUpAchievement('Underdog Lover');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->betRepository->method('hasUnderdogLoverHit')->with($this->user)->willReturn(false);
        $checkUnderdogLoverReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckHometownHero()
    {
        $checkHometownHeroReflection = $this->setUpReflection('checkHometownHero');
        $achievement = $this->setUpAchievement('Hometown Hero');

        //check the happy path
        $team = $this->user->getFavTeam();
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->gameRepository->method('isFinished')->with(18)->willReturn(true);
        $this->betRepository->method('getTeamHits')->with($this->user, $team)->willReturn(17);
        $checkHometownHeroReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testCheckHometownHeroNotEnoughHits()
    {
        $checkHometownHeroReflection = $this->setUpReflection('checkHometownHero');
        $achievement = $this->setUpAchievement('Hometown Hero');

        //check the happy path
        $team = $this->user->getFavTeam();
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->gameRepository->method('isFinished')->with(18)->willReturn(true);
        $this->betRepository->method('getTeamHits')->with($this->user, $team)->willReturn(16);
        $checkHometownHeroReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckHometownHeroSeasonNotFinished()
    {
        $checkHometownHeroReflection = $this->setUpReflection('checkHometownHero');
        $achievement = $this->setUpAchievement('Hometown Hero');

        //check the happy path
        $team = $this->user->getFavTeam();
        $this->assertUserAchievement($this->user, $achievement, false);
        $this->gameRepository->method('isFinished')->with(18)->willReturn(false);
        $this->betRepository->method('getTeamHits')->with($this->user, $team)->willReturn(17);
        $checkHometownHeroReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }

    public function testCheckPrimetimePlayer()
    {
        $checkPrimetimePlayerReflection = $this->setUpReflection('checkPrimetimePlayer');
        $achievement = $this->setUpAchievement('Primetime Player');

        //check the happy path
        $this->assertUserAchievement($this->user, $achievement, false);

        $game = new Game();
        $game->setWeekNumber(1);
        $game->setDate(new \DateTime('2021-09-09 02:00:00'));
        $game->setLocation('heaven');
        $this->entityManager->persist($game);

        $bet = new Bet;
        $bet->setPoints(1);
        $bet->setGame($game);
        $bet->setUser($this->user);
        $this->entityManager->persist($bet);

        $this->entityManager->flush();
        $this->betRepository->method('getPrimetimeBetsForWeek')->with($this->user)->willReturn([$bet]);
        $this->gameRepository->method('getLatestFinishedWeek')->willReturn(1);
        $this->gameRepository->method('getPrimetimeGamesForWeek')->with(1)->willReturn([$game]);

        $checkPrimetimePlayerReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testSundayFunday()
    {
        $checkSundayFundayReflection = $this->setUpReflection('checkSundayFunday');
        $achievement = $this->setUpAchievement('Sunday Funday');

        //not so happy path

        // Saturday Night game
        $game = new Game();
        $game->setWeekNumber(1);
        $game->setDate(new \DateTime('2024-09-15 02:00:00'));
        $game->setLocation('heaven');
        $this->entityManager->persist($game);

        $bet = new Bet;
        $bet->setPoints(1);
        $bet->setGame($game);
        $bet->setUser($this->user);

        $this->entityManager->persist($bet);
        $this->entityManager->flush();

        //check the not so happy path
        $this->assertUserAchievement($this->user, $achievement, false);

        $this->gameRepository->method('getSundayGamesForWeek')->with(1)->willReturn([$game]);
        $this->gameRepository->method('getLatestFinishedWeek')->willReturn(1);
        $this->betRepository->method('getSundayBetsForWeek')->with($this->user, 1)->willReturn([$bet]);

        $checkSundayFundayReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, true);
    }

    public function testSundayFundayNoSundayBet()
    {

        $checkSundayFundayReflection = $this->setUpReflection('checkSundayFunday');
        $achievement = $this->setUpAchievement('Sunday Funday');

        //not so happy path

        // Saturday Night game
        $game = new Game();
        $game->setWeekNumber(1);
        $game->setDate(new \DateTime('2024-09-15 02:00:00'));
        $game->setLocation('heaven');
        $this->entityManager->persist($game);

        $bet = new Bet;
        $bet->setPoints(1);
        $bet->setGame($game);
        $bet->setUser($this->user);

        $this->entityManager->persist($bet);
        $this->entityManager->flush();

        //check the not so happy path
        $this->assertUserAchievement($this->user, $achievement, false);

        $this->gameRepository->method('getSundayGamesForWeek')->with(1)->willReturn([$game]);
        $this->gameRepository->method('getLatestFinishedWeek')->willReturn(1);
        $this->betRepository->method('getSundayBetsForWeek')->with($this->user, 1)->willReturn([]);

        $checkSundayFundayReflection->invoke($this->achievementChecker, $this->user);
        $this->assertUserAchievement($this->user, $achievement, false);
    }
}
