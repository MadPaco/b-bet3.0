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
}
