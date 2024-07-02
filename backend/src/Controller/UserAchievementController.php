<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\UserAchievement;
use App\Entity\Achievement;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserAchievementController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/userAchievement/{username}/fetchAll', name: 'fetch_all_user_achievements', methods: ['GET'])]
    public function fetchAllUserAchievements($username): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found!'], 404);
        }

        $userAchievements = $this->entityManager->getRepository(UserAchievement::class)->findBy(['user' => $user]);
        if (!$userAchievements) {
            return new JsonResponse(['message' => 'No achievements found for this user!'], 200);
        }
        return new JsonResponse($userAchievements, 200);
    }

    #[Route('/api/userAchievement/{username}/fetchLatest', name: 'fetch_latest_user_achievement', methods: ['GET'])]
    public function fetchLatestUserAchievement($username)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found!'], 404);
        }

        $latestUserAchievement = $this->entityManager->getRepository(UserAchievement::class)->findOneBy(['user' => $user], ['dateEarned' => 'DESC']);
        if (!$latestUserAchievement) {
            return new JsonResponse(['message' => 'No achievements found for this user!'], 200);
        }
        return new JsonResponse([
            'name' => $latestUserAchievement->getAchievement()->getName(),
            'dateEarned' => $latestUserAchievement->getDateEarned(),
            'flavorText' => $latestUserAchievement->getAchievement()->getFlavorText(),
            'image' => $latestUserAchievement->getAchievement()->getImage(),
        ], 200);
    }
}
