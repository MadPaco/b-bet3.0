<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Bet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResultsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/game/submitResults', name: 'update_results', methods: ['POST'])]
    public function updateResults(Request $request): Response
    {
        // Update game results and calculate points
        $data = json_decode($request->getContent(), true);
        $updatedGames = [];
        foreach ($data as $gameID => $game) {
            $gameEntity = $this->entityManager->getRepository(Game::class)->find($gameID);
            // keep track of the updated games to calculate the points for those games
            // only and reduce the queries
            array_push($updatedGames, $gameEntity);
            $gameEntity->setHomeScore($game['homeTeamScore']);
            $gameEntity->setAwayScore($game['awayTeamScore']);
            $this->entityManager->persist($gameEntity);
            $this->entityManager->flush();
        }

        $this->updatePoints($updatedGames);
        return new Response('Results updated successfully', 200);
    }

    public function updatePoints($updatedGames)
    {
        // Calculate points for each user
        $users = $this->entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            return new Response('No users found', 404);
        }
        foreach ($users as $user) {
            $points = 0;
            foreach ($updatedGames as $game) {
                if (!$game) {
                    return new Response('Game not found', 404);
                }
                $prediction = $this->entityManager->getRepository(Bet::class)->findOneBy(['user' => $user, 'game' => $game]);
                if (!$prediction) {
                    return new Response('Prediction not found', 404);
                }
                if ($prediction) {
                    if ($prediction->getHomePrediction() === $game->getHomeScore() && $prediction->getAwayPrediction() === $game->getAwayScore()) {
                        $points = 5;
                    } elseif ($prediction->getHomePrediction() - $prediction->getAwayPrediction() === $game->getHomeScore() - $game->getAwayScore()) {
                        $points = 3;
                    } elseif (($prediction->getHomePrediction() > $prediction->getAwayPrediction() && $game->getHomeScore() > $game->getAwayScore()) 
                    || ($prediction->getHomePrediction() < $prediction->getAwayPrediction() && $game->getHomeScore() < $game->getAwayScore())) {
                        $points = 1;
                    }
                }
            }
            $prediction->setPoints($points);
            $this->entityManager->persist($prediction);
        }
        $this->entityManager->flush();
    }
}

?>