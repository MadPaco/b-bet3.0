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

    #[Route('/api/userAchievement/{username}/fetchThreeLatest', name: 'fetch_latest_user_achievement', methods: ['GET'])]
    public function fetchLatestUserAchievement($username)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found!'], 404);
        }

        $latestUserAchievements = $this->entityManager->getRepository(UserAchievement::class)->findBy(['user' => $user], ['dateEarned' => 'DESC']);
        if (!$latestUserAchievements) {
            return new JsonResponse(['message' => 'No achievements found for this user!'], 200);
        }
        $threeLatestAchievements = [];
        for ($i = 0; $i < 3; $i++) {
            $threeLatestAchievements[$i] = [
                'name' => $latestUserAchievements[$i]->getAchievement()->getName(),
                'dateEarned' => $latestUserAchievements[$i]->getDateEarned()->format('d-m-Y H:i'),
                'flavorText' => $latestUserAchievements[$i]->getAchievement()->getFlavorText(),
                'description' => $latestUserAchievements[$i]->getAchievement()->getDescription(),
                'image' => $latestUserAchievements[$i]->getAchievement()->getImage(),
            ];
        }
        return new JsonResponse($threeLatestAchievements);
    }

    #[Route('api/userAchievement/{username}/fetchNonHiddenCompletion', name: 'fetch_non_hidden_completion', methods: ['GET'])]
    public function fetchNonHiddenCompletion($username)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        $nonHiddenAchievements = $this->entityManager->getRepository(Achievement::class)->findBy(['hidden' => false]);
        $nonHiddenAchievementsCount = count($nonHiddenAchievements);
        $userAchievements = $this->entityManager->getRepository(UserAchievement::class)->findBy(['user' => $user]);
        $userNonHiddenAchievements = 0;
        foreach ($userAchievements as $userAchievement) {
            if (!$userAchievement->getAchievement()->getHidden()) {
                $userNonHiddenAchievements++;
            }
        }
        return new JsonResponse(['earned' => $userNonHiddenAchievements, 'total' => $nonHiddenAchievementsCount]);
    }

    #[Route('api/userAchievement/{username}/fetchHiddenCompletion', name: 'fetch_hidden_completion', methods: ['GET'])]
    public function fetchHiddenCompletion($username)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        $hiddenAchievements = $this->entityManager->getRepository(Achievement::class)->findBy(['hidden' => true]);
        $hiddenAchievementsCount = count($hiddenAchievements);
        $userAchievements = $this->entityManager->getRepository(UserAchievement::class)->findBy(['user' => $user]);
        $userHiddenAchievements = 0;
        foreach ($userAchievements as $userAchievement) {
            if ($userAchievement->getAchievement()->getHidden()) {
                $userHiddenAchievements++;
            }
        }
        return new JsonResponse(['earned' => $userHiddenAchievements, 'total' => $hiddenAchievementsCount]);
    }
}
