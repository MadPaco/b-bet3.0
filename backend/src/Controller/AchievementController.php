<?php

namespace App\Controller;

use App\Entity\Achievement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\UserAchievement;


// Achievements are awarded at two different places
// 1. When a user makes a prediction
// 2. When the results are submitted and the points are calculated

// Case 1 Achievements:
// Seasoned Pro (50 predictions)
// Expert (100 predictions)
// Gridiron Guru (200 predictions)
// Hall of Famer (all regular season predictions)
// Early bird (place all predictions for the week 24hours before the first game)


// Case 2 Achievements:
// All other achievements

class AchievementController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/achievements/fetchAll', name: 'fetch_all_achievements', methods: ['GET'])]
    public function getAllAchievements(): JsonResponse
    {
        $achievements = $this->entityManager->getRepository(Achievement::class)->findAll();
        $response = [];

        foreach ($achievements as $achievement) {
            $response[] = [
                'id' => $achievement->getId(),
                'name' => $achievement->getName(),
                'description' => $achievement->getDescription(),
                'image' => $achievement->getImage(),
                'flavorText' => $achievement->getFlavorText(),
                'category' => $achievement->getCategory(),
            ];
        }

        return new JsonResponse($response, 200);
    }
}
