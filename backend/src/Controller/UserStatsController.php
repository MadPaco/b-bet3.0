<?php 
namespace App\Controller;

use App\Entity\User;
use App\Entity\Bet;
use App\Entity\Game;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class UserStatsController extends AbstractController
{
    private$entityManager; 

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('api/stats/{username}', name: 'fetch_user_stats', methods: ['GET'])]
    public function fetchUserStats(string $username): Response
    {
        $NFLWEEKS = 22;
        // Fetch the user by username
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Count the number of bets placed by the user
        $betsPlaced = $this->entityManager->getRepository(Bet::class)->count(['user' => $user]);

        // points per week
        $pointsPerWeek = [];
        for ($i = 1; $i <= $NFLWEEKS; $i++) {
            $points = 0;
            $betsInWeek = $this->entityManager->getRepository(Bet::class)->findBetsByWeeknumber($i);
            $betsInWeekByUser = array_filter($betsInWeek, function ($bet) use ($user) {
                return $bet->getUser() === $user;
            });
            foreach ($betsInWeekByUser as $bet) {
                $points += $bet->getPoints();
            }
            $pointsPerWeek[$i] = $points;
        }

        // Return the collection of stats
        return new JsonResponse([
            'bets_placed' => $betsPlaced,
            'points_per_week' => $pointsPerWeek,
        ]);
    }
}


?>