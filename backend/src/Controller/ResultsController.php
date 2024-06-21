<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Bet;
use App\Entity\NflTeam;
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

        //$response = $this->validator->validateData($request->getContent());
        //if ($response) {
        //    return $response;
        //}


        // Update game results and calculate points
        $data = json_decode($request->getContent(), true);

        
        $updatedGames = [];
        foreach ($data as $gameID => $game) {
            $gameEntity = $this->entityManager->getRepository(Game::class)->find($gameID);
        
            if ($game['homeTeamScore'] === null || $game['awayTeamScore'] === null) {
                continue;
            }
            
            array_push($updatedGames, $game);
            
            $homeTeamID = $gameEntity->getHomeTeam();
            $awayTeamID = $gameEntity->getAwayTeam();
            
            $homeTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['id' => $homeTeamID]);
            $awayTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['id' => $awayTeamID]);
        
            if (!$homeTeam || !$awayTeam) {
                if (!$homeTeam) {
                    error_log('Home team not found: ' . $game['homeTeam']);
                }
                if (!$awayTeam) {
                    error_log('Away team not found: ' . $game['awayTeam']);
                }
                return new Response('Team not found', 404);
            }
            
            // Load previous scores to avoid awarding a win/loss both or twice
            $prevHomeScore = $gameEntity->getHomeScore();
            $prevAwayScore = $gameEntity->getAwayScore();
        
            // Revert previous records
            $homeTeam->setPointsFor($homeTeam->getPointsFor() - $prevHomeScore);
            $homeTeam->setPointsAgainst($homeTeam->getPointsAgainst() - $prevAwayScore);
            $awayTeam->setPointsFor($awayTeam->getPointsFor() - $prevAwayScore);
            $awayTeam->setPointsAgainst($awayTeam->getPointsAgainst() - $prevHomeScore);
        
            if ($prevHomeScore > $prevAwayScore) {
                $homeTeam->setWins($homeTeam->getWins() - 1);
                $awayTeam->setLosses($awayTeam->getLosses() - 1);
            } elseif ($prevHomeScore < $prevAwayScore) {
                $homeTeam->setLosses($homeTeam->getLosses() - 1);
                $awayTeam->setWins($awayTeam->getWins() - 1);
            } elseif ($prevHomeScore === $prevAwayScore && $prevHomeScore !== null && $prevAwayScore !== null){
                $homeTeam->setTies($homeTeam->getTies() - 1);
                $awayTeam->setTies($awayTeam->getTies() - 1);
            }
        
            // Update scores and records with new values
            $homeTeam->setPointsFor($homeTeam->getPointsFor() + $game['homeTeamScore']);
            $homeTeam->setPointsAgainst($homeTeam->getPointsAgainst() + $game['awayTeamScore']);
            $awayTeam->setPointsFor($awayTeam->getPointsFor() + $game['awayTeamScore']);
            $awayTeam->setPointsAgainst($awayTeam->getPointsAgainst() + $game['homeTeamScore']);
            $homeTeam->setNetPoints($homeTeam->getPointsFor() - $homeTeam->getPointsAgainst());
            $awayTeam->setNetPoints($awayTeam->getPointsFor() - $awayTeam->getPointsAgainst());
        
            if ($game['homeTeamScore'] > $game['awayTeamScore']) {
                $homeTeam->setWins($homeTeam->getWins() + 1);
                $awayTeam->setLosses($awayTeam->getLosses() + 1);
            } elseif ($game['homeTeamScore'] < $game['awayTeamScore']) {
                $homeTeam->setLosses($homeTeam->getLosses() + 1);
                $awayTeam->setWins($awayTeam->getWins() + 1);
            } elseif ($game['homeTeamScore'] === $game['awayTeamScore']) {
                $homeTeam->setTies($homeTeam->getTies() + 1);
                $awayTeam->setTies($awayTeam->getTies() + 1);
            }

        
            $gameEntity->setHomeScore($game['homeTeamScore']);
            $gameEntity->setAwayScore($game['awayTeamScore']);
            
            $this->entityManager->persist($homeTeam);
            $this->entityManager->persist($awayTeam);
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