<?php

namespace App\Tests\Service;

use App\Entity\Achievement;
use App\Entity\Bet;
use App\Entity\User;
use App\Entity\UserAchievement;
use App\Service\PredictionsAchievementChecker;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PredictionsAchievementCheckerTest extends KernelTestCase
{
    private $entityManager;
    private $checker;
    private $betRepository;
    private $achievementRepository;
    private $userAchievementRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Mocking the repositories using EntityRepository for type compatibility
        $this->betRepository = $this->createMock(EntityRepository::class);
        $this->achievementRepository = $this->createMock(EntityRepository::class);
        $this->userAchievementRepository = $this->createMock(EntityRepository::class);

        // Setting up the EntityManager to return these repositories
        $this->entityManager->method('getRepository')->willReturnMap([
            [Bet::class, $this->betRepository],
            [Achievement::class, $this->achievementRepository],
            [UserAchievement::class, $this->userAchievementRepository],
        ]);

        $this->checker = new PredictionsAchievementChecker($this->entityManager);
    }

    private function callMethod($obj, $methodName, array $args = [])
    {
        $reflection = new \ReflectionClass(get_class($obj));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }

    public function testCheckSeasonedProAchievement()
    {
        $user = new User();
        $achievement = new Achievement();
        $achievement->setName('Seasoned Pro');

        $this->achievementRepository->method('findOneBy')
            ->with(['name' => 'Seasoned Pro'])
            ->willReturn($achievement);

        $this->userAchievementRepository->method('findOneBy')
            ->with(['user' => $user, 'achievement' => $achievement])
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->callMethod($this->checker, 'checkSeasonedPro', [$user, 50]);

        $this->assertTrue($result);
    }

    public function testCheckEarlyBirdAchievement()
    {
        $user = new User();
        $achievement = new Achievement();
        $achievement->setName('Early Bird');

        $this->betRepository->method('findLatestCompletedWeekNumber')
            ->willReturn(1);

        $this->betRepository->method('getEarliestGameDate')
            ->with(1)
            ->willReturn((new \DateTime())->modify('+2 days'));

        $this->achievementRepository->method('findOneBy')
            ->with(['name' => 'Early Bird'])
            ->willReturn($achievement);

        $this->userAchievementRepository->method('findOneBy')
            ->with(['user' => $user, 'achievement' => $achievement])
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->callMethod($this->checker, 'checkEarlyBird', [$user]);

        $this->assertTrue(true);
    }
}
