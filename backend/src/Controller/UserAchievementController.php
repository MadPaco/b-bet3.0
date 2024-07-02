<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\UserAchievement;
use App\Entity\Achievement;
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

    #[Route('/api/userAchievements/fetchAll', name: 'fetch_all_user_achievements', methods: ['GET'])]
    public function getAllUserAchievements(): JsonResponse
    {
        $userAchievements = $this->entityManager->getRepository(UserAchievement::class)->findAll();
        $response = [];

        foreach ($userAchievements as $userAchievement) {
            $response[] = [
                'id' => $userAchievement->getId(),
                'user' => $userAchievement->getUser(),
                'achievement' => $userAchievement->getAchievement(),
                'dateEarned' => $userAchievement->getDateEarned(),
            ];
        }

        return new JsonResponse($response, 200);
    }

    #[Route('/api/userAchievements/fetchByUser/{userID}', name: 'fetch_user_achievements', methods: ['GET'])]
    public function getUserAchievements(int $userID): JsonResponse
    {
        $userAchievements = $this->entityManager->getRepository(UserAchievement::class)->findBy(['Id' => $userID]);
        $response = [];

        foreach ($userAchievements as $userAchievement) {
            $response[] = [
                'id' => $userAchievement->getId(),
                'user' => $userAchievement->getUser(),
                'achievement' => $userAchievement->getAchievement(),
                'dateEarned' => $userAchievement->getDateEarned(),
            ];
        }

        return new JsonResponse($response, 200);
    }
}
