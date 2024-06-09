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
use App\Service\ResultValidator;

class ResultsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ResultValidator $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/api/game/fetchResults', name: 'fetch_results', methods: ['GET'])]
    public function fetchResults(Request $request): Response
    {
        $weekNumber = $request->query->get('weekNumber');
        $games = $this->entityManager->getRepository(Game::class)->findBy(['weekNumber' => $weekNumber]);
        $results = [];
        foreach ($games as $game) {
            $result = [
                'gameID' => $game->getId(),
                'homeTeamScore' => $game->getHomeScore(),
                'awayTeamScore' => $game->getAwayScore()
            ];
            array_push($results, $result);
        }

        return new JsonResponse($results, 200);
    }

    #[Route('/api/game/submitResults', name: 'update_results', methods: ['POST'])]
    public function updateResults(Request $request): Response
    {
        // Check if the user is an admin
        $user = $this->getUser();
        if (!in_array('ADMIN', $user->getRoles())) {
            return new Response('Unauthorized', 401);
        }

        $response = $this->validator->validateData($request->getContent());
        if ($response) {
            return $response;
        }


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
        if (count($updatedGames) === 0) {
            return new Response('No games found', 404);
        }
        $this->updatePoints($updatedGames);
        return new Response('success', 200);
    }

    public function updatePoints($updatedGames)
    {
        // error log showing the updated games:
        error_log('Updated games: ' . count($updatedGames));
        // Calculate points for each user
        $users = $this->entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            return new Response('No users found', 404);
        }
        foreach ($users as $user) {
            error_log('found user');
            foreach ($updatedGames as $game) {
                error_log('found game' . $game->getId());
                if (!$game) {
                    return new Response('Game not found', 404);
                }
                $prediction = $this->entityManager->getRepository(Bet::class)->findOneBy(['user' => $user, 'game' => $game]);
                if (!$prediction) {
                    // this skip is needed in case the user has not made predictions for all games
                    error_log('Prediction not found, skipping');
                    continue;
                }
                $points = 0;
                error_log('Updating points...');
                if ($prediction->getHomePrediction() === $game->getHomeScore() && $prediction->getAwayPrediction() === $game->getAwayScore()) {
                    $points = 5;
                    error_log("Condition 1 met: assigning 5 points");
                } elseif ($prediction->getHomePrediction() - $prediction->getAwayPrediction() === $game->getHomeScore() - $game->getAwayScore()) {
                    $points = 3;
                    error_log("Condition 2 met: assigning 3 points");
                } elseif (($prediction->getHomePrediction() > $prediction->getAwayPrediction() && $game->getHomeScore() > $game->getAwayScore()) 
                || ($prediction->getHomePrediction() < $prediction->getAwayPrediction() && $game->getHomeScore() < $game->getAwayScore())) {
                    $points = 1;
                    error_log("Condition 3 met: assigning 1 point");
                } else {
                    error_log("No conditions met: assigning 0 points");
                }
                $prediction->setPoints($points);
                $this->entityManager->persist($prediction);
                $this->entityManager->flush();
            }
        }

    }
}

?>