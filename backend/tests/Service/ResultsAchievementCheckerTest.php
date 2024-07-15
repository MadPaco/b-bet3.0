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
use App\Repository\GameRepository;
use App\Entity\Bet;
use App\Entity\Game;
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


    public function testCheckFirstDown()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'First Down']);
        assertNotNull($achievement);

        $firstDownReflection = $this->reflectedChecker->getMethod('checkFirstDown');
        assertNotNull($firstDownReflection);
        $firstDownReflection->setAccessible(true);

        //this will return a hit meaning that the achievement should be awarded 
        //after executing checkFirstDown
        $bet = new Bet;
        $this->betRepository->method('findHitsByUser')->with($this->user)->willReturn([$bet]);

        //check that the user doesn"t have the achievement yet
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNull($userAchievement);

        //invoke the check
        $firstDownReflection->invoke($this->achievementChecker, $this->user);

        //check that the achievement has beeen awarded
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckTwoMinuteDrill()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Two-Minute Drill']);
        assertNotNull($achievement);

        $twoMinuteDrillReflection = $this->reflectedChecker->getMethod('checkTwoMinuteDrill');
        assertNotNull($twoMinuteDrillReflection);
        $twoMinuteDrillReflection->setAccessible(true);

        //check that the user doesn"t have the achievement yet
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNull($userAchievement);

        $bet = new Bet;
        $this->betRepository->method('findTwoMinuteDrillHit')->with($this->user)->willReturn([$bet]);
        $twoMinuteDrillReflection->invoke($this->achievementChecker, $this->user);

        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckTrickPlay()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Trick Play']);
        assertNotNull($achievement);

        $trickPlayReflection = $this->reflectedChecker->getMethod('checkTrickPlay');
        assertNotNull($trickPlayReflection);
        $trickPlayReflection->setAccessible(true);

        //check that the user doesn"t have the achievement yet
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNull($userAchievement);

        $this->betRepository->method('hasTrickPlayHit')->with($this->user)->willReturn(true);
        $trickPlayReflection->invoke($this->achievementChecker, $this->user);

        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckPigskinProphet()
    {
        $pigskinProphetReflection = $this->reflectedChecker->getMethod('checkPigskinProphet');
        assertNotNull($pigskinProphetReflection);
        $pigskinProphetReflection->setAccessible(true);

        // there are 2 games in the db for week 1, but no scores
        // check that the user doesn"t have the achievement yet
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $this->achievementRepository->findOneBy(['name' => 'Pigskin Prophet'])
            ]
        );
        assertNull($userAchievement);

        //invoke the check
        $pigskinProphetReflection->invoke($this->achievementChecker, $this->user);
        //check that the achievement has not been awarded
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $this->achievementRepository->findOneBy(['name' => 'Pigskin Prophet'])
            ]
        );
        assertNull($userAchievement);

        //set the user to have a hit in the pigskin prophet
        $this->betRepository->method('hasPigskinProphetHit')->with($this->user)->willReturn(true);

        //invoke the check
        $pigskinProphetReflection->invoke($this->achievementChecker, $this->user);
        //check that the achievement has been awarded
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $this->achievementRepository->findOneBy(['name' => 'Pigskin Prophet'])
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckTouchdown()
    {
        $touchdownReflection = $this->reflectedChecker->getMethod('checkTouchdown');
        assertNotNull($touchdownReflection);
        $touchdownReflection->setAccessible(true);

        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $this->achievementRepository->findOneBy(['name' => 'Touchdown'])
            ]
        );
        assertNull($userAchievement);

        $bet = new Bet;
        $bet->setPoints(7);
        $this->betRepository->method('findHitsByUser')->with($this->user)->willReturn([$bet]);

        $touchdownReflection->invoke($this->achievementChecker, $this->user);
        //check that the achievement has been awarded
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $this->achievementRepository->findOneBy(['name' => 'Touchdown'])
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testPickSix()
    {
        $pickSixReflection = $this->reflectedChecker->getMethod('checkPickSix');
        assertNotNull($pickSixReflection);
        $pickSixReflection->setAccessible(true);

        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $this->achievementRepository->findOneBy(['name' => 'Pick Six'])
            ]
        );
        assertNull($userAchievement);
        $this->gameRepository->method('findLatestWeekWithResults')->willReturn(1);
        $this->betRepository->method('getCountOfHitsByUserForGivenWeek')->with($this->user, 1)->willReturn(6);

        $pickSixReflection->invoke($this->achievementChecker, $this->user);
        //check that the achievement has been awarded
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $this->achievementRepository->findOneBy(['name' => 'Pick Six'])
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testPickSixNoResults()
    { {
            $pickSixReflection = $this->reflectedChecker->getMethod('checkPickSix');
            assertNotNull($pickSixReflection);
            $pickSixReflection->setAccessible(true);

            $userAchievement = $this->userAchievementRepository->findOneBy(
                [
                    'user' => $this->user,
                    'achievement' => $this->achievementRepository->findOneBy(['name' => 'Pick Six'])
                ]
            );
            assertNull($userAchievement);
            // if findLatestWeekWithResults return 0 there are not results set yet
            $this->gameRepository->method('findLatestWeekWithResults')->willReturn(0);
            $this->betRepository->method('getCountOfHitsByUserForGivenWeek')->with($this->user, 0)->willReturn(6);

            $pickSixReflection->invoke($this->achievementChecker, $this->user);
            //check that the achievement has been awarded
            $userAchievement = $this->userAchievementRepository->findOneBy(
                [
                    'user' => $this->user,
                    'achievement' => $this->achievementRepository->findOneBy(['name' => 'Pick Six'])
                ]
            );
            assertNull($userAchievement);
        }
    }
}
