<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Entity\Achievement;
use App\Entity\Bet;
use App\Repository\AchievementRepository;
use App\Repository\BetRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use App\Service\ResultsAchievementChecker;
use Doctrine\Common\Collections\ArrayCollection;

class ResultsAchievementCheckerTest extends TestCase
{
    private $entityManager;
    private $achievementRepository;
    private $betRepository;
    private $ResultsAchievementChecker;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->achievementRepository = $this->createMock(AchievementRepository::class);
        $this->betRepository = $this->createMock(BetRepository::class);

        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Achievement::class, $this->achievementRepository],
                [Bet::class, $this->betRepository],
            ]);

        $this->ResultsAchievementChecker = new ResultsAchievementChecker($this->entityManager);
    }

    public function testCheckTwoMinuteDrillNoAchievement(): void
    {
        $user = $this->createMock(User::class);

        $this->achievementRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Two Minute Drill'])
            ->willReturn(null);

        $this->betRepository->expects($this->never())
            ->method('findTwoMinuteDrillHit');

        $this->ResultsAchievementChecker->checkTwoMinuteDrill($user);
    }

    public function testCheckTwoMinuteDrillHasAchievement(): void
    {
        $user = $this->createMock(User::class);
        $achievement = $this->createMock(Achievement::class);

        $this->achievementRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Two Minute Drill'])
            ->willReturn($achievement);

        $this->ResultsAchievementChecker->expects($this->once())
            ->method('hasAchievement')
            ->with($user, $achievement)
            ->willReturn(true);

        $this->betRepository->expects($this->never())
            ->method('findTwoMinuteDrillHit');

        $this->ResultsAchievementChecker->checkTwoMinuteDrill($user);
    }

    public function testCheckTwoMinuteDrillAwardAchievement(): void
    {
        $user = $this->createMock(User::class);
        $achievement = $this->createMock(Achievement::class);
        $bet = $this->createMock(Bet::class);

        $this->achievementRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Two Minute Drill'])
            ->willReturn($achievement);

        $this->ResultsAchievementChecker->expects($this->once())
            ->method('hasAchievement')
            ->with($user, $achievement)
            ->willReturn(false);

        $this->betRepository->expects($this->once())
            ->method('findTwoMinuteDrillHit')
            ->with($user)
            ->willReturn(new ArrayCollection([$bet]));

        $this->ResultsAchievementChecker->expects($this->once())
            ->method('awardAchievement')
            ->with($user, $achievement);

        $this->ResultsAchievementChecker->checkTwoMinuteDrill($user);
    }

    public function testCheckTwoMinuteDrillNoHits(): void
    {
        $user = $this->createMock(User::class);
        $achievement = $this->createMock(Achievement::class);

        $this->achievementRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Two Minute Drill'])
            ->willReturn($achievement);

        $this->ResultsAchievementChecker->expects($this->once())
            ->method('hasAchievement')
            ->with($user, $achievement)
            ->willReturn(false);

        $this->betRepository->expects($this->once())
            ->method('findTwoMinuteDrillHit')
            ->with($user)
            ->willReturn(new ArrayCollection([]));

        $this->ResultsAchievementChecker->expects($this->never())
            ->method('awardAchievement');

        $this->ResultsAchievementChecker->checkTwoMinuteDrill($user);
    }
}
