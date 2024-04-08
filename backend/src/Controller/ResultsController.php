<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
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

        //$this->updatePoints($updatedGames);
        return new Response('Results updated successfully', 200);
    }

    public function updatePoints($updatedGames)
    {
        // Calculate points for each user
        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $points = 0;
            foreach ($updatedGames as $game) {
                $prediction = $this->entityManager->getRepository(Bet::class)->findOneBy(['user' => $user, 'game' => $game]);
                if ($prediction) {
                    if ($prediction->getHomeScore() === $game->getHomeScore() && $prediction->getAwayScore() === $game->getAwayScore()) {
                        $points += 5;
                    } elseif ($prediction->getHomeScore() - $prediction->getAwayScore() === $game->getHomeScore() - $game->getAwayScore()) {
                        $points += 3;
                    } elseif (($prediction->getHomeScore() > $prediction->getAwayScore() && $game->getHomeScore() > $game->getAwayScore()) || ($prediction->getHomeScore() < $prediction->getAwayScore() && $game->getHomeScore() < $game->getAwayScore())) {
                        $points += 1;
                    }
                }
            }
            $prediciont->setPoints($points);
            $this->entityManager->persist($prediction);
        }
        $this->entityManager->flush();
    }
}

?>