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

    private function getEarnedPercentage(Achievement $achievement): float
    {
        $userCount = $this->entityManager->getRepository(User::class)->count([]);
        $userAchievements = $this->entityManager->getRepository(UserAchievement::class)->findBy([
            'achievement' => $achievement
        ]);
        if ($userCount === 0) {
            return 0;
        }
        return count($userAchievements) / $userCount * 100;
    }

    #[Route('/api/achievements/{username}/fetchNonHidden', name: 'fetch_all_achievements', methods: ['GET'])]
    public function fetchAllAchievements($username): JsonResponse
    {
        // by default only send the achievements that are not hidden
        $achievements = $this->entityManager->getRepository(Achievement::class)->findAll();
        $response = [];


        foreach ($achievements as $achievement) {

            if ($achievement->getHidden()) {
                continue;
            }

            $userAchievement = $this->entityManager->getRepository(UserAchievement::class)->findOneBy([
                'user' => $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]),
                'achievement' => $achievement
            ]);

            $earnedPercentage = $this->getEarnedPercentage($achievement);

            $response[] = [
                'id' => $achievement->getId(),
                'name' => $achievement->getName(),
                'description' => $achievement->getDescription(),
                'image' => $achievement->getImage(),
                'flavorText' => $achievement->getFlavorText(),
                'category' => $achievement->getCategory(),
                'hidden' => $achievement->getHidden(),
                'dateEarned' => $userAchievement ? $userAchievement->getDateEarned()->format('d-m-Y H:i') : null,
                'earnedPercentage' => $earnedPercentage
            ];
        }

        // order them so the earned achievements are at the top
        usort($response, function ($a, $b) {
            if ($a['dateEarned'] === null && $b['dateEarned'] === null) {
                return 0;
            }
            if ($a['dateEarned'] === null) {
                return 1;
            }
            if ($b['dateEarned'] === null) {
                return -1;
            }
            return strtotime($b['dateEarned']) - strtotime($a['dateEarned']);
        });
        return new JsonResponse($response, 200);
    }

    #[Route('api/achievements/{username}/fetchHidden', name: 'fetch_hidden_achievements', methods: ['GET'])]
    public function fetchHiddenAchievements($username): JsonResponse
    {
        // only send the achievements that the user currently has achieved
        // this should prevent to show the hidden achievements to the user
        // even is he inspects the network traffic
        // I doubt someone would do that but better safe than sorry
        $achievements = $this->entityManager->getRepository(Achievement::class)->findAll();
        $response = [];

        foreach ($achievements as $achievement) {
            if (!$achievement->getHidden()) {
                continue;
            }

            $userAchievement = $this->entityManager->getRepository(UserAchievement::class)->findOneBy([
                'user' => $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]),
                'achievement' => $achievement
            ]);

            if (!$userAchievement) {
                continue;
            }

            $earnedPercentage = $this->getEarnedPercentage($achievement);

            $response[] = [
                'id' => $achievement->getId(),
                'name' => $achievement->getName(),
                'description' => $achievement->getDescription(),
                'image' => $achievement->getImage(),
                'flavorText' => $achievement->getFlavorText(),
                'category' => $achievement->getCategory(),
                'hidden' => $achievement->getHidden(),
                'dateEarned' => $userAchievement ? $userAchievement->getDateEarned()->format('d-m-Y H:i') : null,
                'earnedPercentage' => $earnedPercentage
            ];
        }

        return new JsonResponse($response, 200);
    }
}
