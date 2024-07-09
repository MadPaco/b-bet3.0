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
use App\Service\ResultsAchievementChecker;
use App\Service\ResultValidator;

class ResultsController extends AbstractController
{
    private $entityManager;
    private $achievementChecker;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ResultsAchievementChecker $achievementChecker, ResultValidator $validator)
    {
        $this->entityManager = $entityManager;
        $this->achievementChecker = $achievementChecker;
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

        // Validate the JSON structure
        $validationResponse = $this->validator->validateData($request->getContent());
        if ($validationResponse !== null) {
            return $validationResponse;
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['message' => 'Invalid data format'], Response::HTTP_BAD_REQUEST);
        }

        $updatedGames = [];
        foreach ($data as $gameID => $game) {
            if (!is_array($game)) {
                continue; // Skip invalid game data
            }

            $gameEntity = $this->entityManager->getRepository(Game::class)->find($gameID);
            if (!$gameEntity) {
                continue;
            }

            if (!isset($game['homeTeamScore']) || !isset($game['awayTeamScore']) || $game['homeTeamScore'] === null || $game['awayTeamScore'] === null) {
                continue;
            }

            array_push($updatedGames, $gameEntity);

            $homeTeamID = $gameEntity->getHomeTeam();
            $awayTeamID = $gameEntity->getAwayTeam();

            $homeTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['id' => $homeTeamID]);
            $awayTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['id' => $awayTeamID]);

            if (!$homeTeam || !$awayTeam) {
                return new Response('Team not found', 404);
            }

            // Load previous scores to avoid awarding a win/loss both or twice
            $prevHomeScore = $gameEntity->getHomeScore();
            $prevAwayScore = $gameEntity->getAwayScore();

            // Revert previous records
            $homeTeam->setPointsFor($homeTeam->getPointsFor() - intval($prevHomeScore));
            $homeTeam->setPointsAgainst($homeTeam->getPointsAgainst() - intval($prevAwayScore));
            $awayTeam->setPointsFor($awayTeam->getPointsFor() - intval($prevAwayScore));
            $awayTeam->setPointsAgainst($awayTeam->getPointsAgainst() - intval($prevHomeScore));

            if ($prevHomeScore > $prevAwayScore) {
                $homeTeam->setWins($homeTeam->getWins() - 1);
                $awayTeam->setLosses($awayTeam->getLosses() - 1);
            } elseif ($prevHomeScore < $prevAwayScore) {
                $homeTeam->setLosses($homeTeam->getLosses() - 1);
                $awayTeam->setWins($awayTeam->getWins() - 1);
            } elseif ($prevHomeScore === $prevAwayScore && $prevHomeScore !== null && $prevAwayScore !== null) {
                $homeTeam->setTies($homeTeam->getTies() - 1);
                $awayTeam->setTies($awayTeam->getTies() - 1);
            }

            // Update scores and records with new values
            $homeTeamScore = intval($game['homeTeamScore']);
            $awayTeamScore = intval($game['awayTeamScore']);

            $homeTeam->setPointsFor($homeTeam->getPointsFor() + $homeTeamScore);
            $homeTeam->setPointsAgainst($homeTeam->getPointsAgainst() + $awayTeamScore);
            $awayTeam->setPointsFor($awayTeam->getPointsFor() + $awayTeamScore);
            $awayTeam->setPointsAgainst($awayTeam->getPointsAgainst() + $homeTeamScore);
            $homeTeam->setNetPoints($homeTeam->getPointsFor() - $homeTeam->getPointsAgainst());
            $awayTeam->setNetPoints($awayTeam->getPointsFor() - $awayTeam->getPointsAgainst());

            if ($homeTeamScore > $awayTeamScore) {
                $homeTeam->setWins($homeTeam->getWins() + 1);
                $awayTeam->setLosses($awayTeam->getLosses() + 1);
            } elseif ($homeTeamScore < $awayTeamScore) {
                $homeTeam->setLosses($homeTeam->getLosses() + 1);
                $awayTeam->setWins($awayTeam->getWins() + 1);
            } elseif ($homeTeamScore === $awayTeamScore) {
                $homeTeam->setTies($homeTeam->getTies() + 1);
                $awayTeam->setTies($awayTeam->getTies() + 1);
            }

            $gameEntity->setHomeScore($homeTeamScore);
            $gameEntity->setAwayScore($awayTeamScore);

            $this->entityManager->persist($homeTeam);
            $this->entityManager->persist($awayTeam);
            $this->entityManager->persist($gameEntity);
            $this->entityManager->flush();
        }

        if (count($updatedGames) === 0) {
            return new Response('No games found', 404);
        }
        $this->updatePoints($updatedGames);
        $this->achievementChecker->checkAllAchievementsForAllUsers();
        return new Response('success', 200);
    }



    public function updatePoints(array $updatedGames)
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            return new Response('No users found', 404);
        }

        foreach ($users as $user) {
            foreach ($updatedGames as $game) {
                if (!$game instanceof Game) {
                    continue;
                }

                $prediction = $this->entityManager->getRepository(Bet::class)->findOneBy(['user' => $user, 'game' => $game]);
                if (!$prediction) {
                    continue;
                }

                $points = $this->calculatePoints($prediction, $game);
                $prediction->setPoints($points);
                $this->entityManager->persist($prediction);
            }
        }
        $this->entityManager->flush();
    }

    private function calculatePoints(Bet $prediction, Game $game): int
    {
        if ($prediction->getHomePrediction() === $game->getHomeScore() && $prediction->getAwayPrediction() === $game->getAwayScore()) {
            return 5;
        } elseif ($prediction->getHomePrediction() - $prediction->getAwayPrediction() === $game->getHomeScore() - $game->getAwayScore()) {
            return 3;
        } elseif (($prediction->getHomePrediction() > $prediction->getAwayPrediction() && $game->getHomeScore() > $game->getAwayScore())
            || ($prediction->getHomePrediction() < $prediction->getAwayPrediction() && $game->getHomeScore() < $game->getAwayScore())
        ) {
            return 1;
        } else {
            return 0;
        }
    }
}
