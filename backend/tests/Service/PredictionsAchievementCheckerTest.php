<?php

namespace App\Tests\Service;

use App\Repository\AchievementRepository;
use App\Service\PredictionsAchievementChecker;
use App\Repository\BetRepositoryInterface;
use App\Repository\GameRepositoryInterface;
use App\Repository\UserRepository;
use App\Repository\UserAchievementRepository;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class PredictionsAchievementCheckerTest extends WebTestCase
{
    private $achievementChecker;
    private $entityManager;
    private $betRepository;
    private $gameRepository;
    private $userRepository;
    private $achievementRepository;
    private $userAchievementRepository;
    private $reflectedChecker;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        date_default_timezone_set('Europe/Berlin');
        // Boot the kernel to get the container
        self::bootKernel();

        // Fetch the required services from the container
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->betRepository = $this->createMock(BetRepositoryInterface::class);
        $this->gameRepository = $this->createMock(GameRepositoryInterface::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->achievementRepository = self::getContainer()->get(AchievementRepository::class);
        $this->userAchievementRepository = self::getContainer()->get(UserAchievementRepository::class);


        // Instantiate the service with the dependencies
        $this->achievementChecker = new PredictionsAchievementChecker($this->entityManager, $this->gameRepository, $this->betRepository,);
        //reflect the checker to make the functions accessible
        $this->reflectedChecker = new ReflectionClass($this->achievementChecker);
        $this->user = $this->userRepository->findOneBy(['username' => 'admin']);
    }


    public function testSeasonedPro()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Seasoned Pro']);

        $seasonedProReflection = $this->reflectedChecker->getMethod('checkSeasonedPro');
        $seasonedProReflection->setAccessible(true);
        $seasonedProReflection->invoke($this->achievementChecker, $this->user, 50);

        //check for the userAchievement
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckExpert()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Expert']);

        $seasonedProReflection = $this->reflectedChecker->getMethod('checkExpert');
        $seasonedProReflection->setAccessible(true);
        $seasonedProReflection->invoke($this->achievementChecker, $this->user, 100);

        //check for the userAchievement
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckGridironGuru()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Gridiron Guru']);

        $seasonedProReflection = $this->reflectedChecker->getMethod('checkGridironGuru');
        $seasonedProReflection->setAccessible(true);
        $seasonedProReflection->invoke($this->achievementChecker, $this->user, 200);

        //check for the userAchievement
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckHallOfFamer()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Hall of Famer']);

        $seasonedProReflection = $this->reflectedChecker->getMethod('checkHallOfFamer');
        $seasonedProReflection->setAccessible(true);
        $seasonedProReflection->invoke($this->achievementChecker, $this->user, 272);

        //check for the userAchievement
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }


    // test early bird:
    // Place all predictions for a week more than 24h before the first kickoff
    public function testCheckEarlyBirdWithOneSecondSpare()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Early Bird']);
        assertNotNull($achievement);

        // Mock the BetRepository and GameRepository methods
        $this->betRepository->method('findLatestCompletedWeekNumber')->willReturn(1);
        $mockedTime = (new \DateTime())->modify('+24 hours')->modify('+1 second');
        $this->gameRepository->method('getEarliestGameDate')->with(1)->willReturn($mockedTime);

        // check that the user doesn't have the achievement yet
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNull($userAchievement);

        $earlyBirdReflection = $this->reflectedChecker->getMethod('checkEarlyBird');
        $earlyBirdReflection->setAccessible(true);
        $earlyBirdReflection->invoke($this->achievementChecker, $this->user);

        // Check for the userAchievement
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNotNull($userAchievement);
    }

    public function testCheckEarlyBirdWithOneSecondShort()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Early Bird']);
        assertNotNull($achievement);

        // Mock the BetRepository and GameRepository methods
        $this->betRepository->method('findLatestCompletedWeekNumber')->willReturn(1);
        $mockedTime = (new \DateTime())->modify('+23 hours')->modify('+59 minute')->modify('+59 seconds');
        $this->gameRepository->method('getEarliestGameDate')->with(1)->willReturn($mockedTime);

        // check that the user doesn't have the achievement yet
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNull($userAchievement);

        $earlyBirdReflection = $this->reflectedChecker->getMethod('checkEarlyBird');
        $earlyBirdReflection->setAccessible(true);
        $earlyBirdReflection->invoke($this->achievementChecker, $this->user);

        // Check that the achievement has not been awarded
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNull($userAchievement);
    }

    public function testCheckEarlyBirdWithExactly24Hours()
    {
        $achievement = $this->achievementRepository->findOneBy(['name' => 'Early Bird']);
        assertNotNull($achievement);

        // Mock the BetRepository and GameRepository methods
        $this->betRepository->method('findLatestCompletedWeekNumber')->willReturn(1);
        $mockedTime = (new \DateTime())->modify('+24 hours');
        $this->gameRepository->method('getEarliestGameDate')->with(1)->willReturn($mockedTime);

        // check that the user doesn't have the achievement yet
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        assertNull($userAchievement);

        $earlyBirdReflection = $this->reflectedChecker->getMethod('checkEarlyBird');
        $earlyBirdReflection->setAccessible(true);
        $earlyBirdReflection->invoke($this->achievementChecker, $this->user);

        // Check that the achievement has been awarded
        $userAchievement = $this->userAchievementRepository->findOneBy(
            [
                'user' => $this->user,
                'achievement' => $achievement
            ]
        );
        // 24 hours should fail this because the text reads:
        //Place all predictions for a week more than 24h before the first kickoff
        assertNull($userAchievement);
    }
}
