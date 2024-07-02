<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\NflTeam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ScheduleController extends AbstractController
{
    private $entityManager;
    private $request;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/schedule', name: 'get_schedule', methods: ['GET'])]
    public function getSchedule(Request $request): Response
    {

        $weekNumber = $request->query->get('weekNumber');
        //get the games from a specific week is we have submitted a weekNumber, else get all games
        //order the by date for the frontend
        if ($weekNumber !== null) {
            $games = $this->entityManager->getRepository(Game::class)->findBy(['weekNumber' => $weekNumber], ['date' => 'ASC']);
        } else {
            $games = $this->entityManager->getRepository(Game::class)->findAll(['date' => 'ASC']);
        }

        $schedule = [];
        foreach ($games as $game) {
            $schedule[] = [
                'id' => $game->getId(),
                'weekNumber' => $game->getWeekNumber(),
                'date' => $game->getDate()->format('Y-m-d H:i:s'),
                'location' => $game->getLocation(),
                'homeTeam' => $game->getHomeTeam()->getName(),
                'homeTeamLogo' => $game->getHomeTeam()->getLogo(),
                'awayTeam' => $game->getAwayTeam()->getName(),
                'awayTeamLogo' => $game->getAwayTeam()->getLogo(),
                'homeOdds' => $game->getHomeOdds(),
                'awayOdds' => $game->getAwayOdds(),
                'overUnder' => $game->getOverUnder(),
                'homeScore' => $game->getHomeScore(),
                'awayScore' => $game->getAwayScore()

            ];
        }

        return $this->json($schedule);
    }

    #[Route('/api/schedule/upcomingGames', name: 'fetch_upcoming_games', methods: ['GET'])]
    public function fetchUpcomingGames(): JsonResponse
    {
        // Find the games ordered by date in ascending order
        $upcomingGames = $this->entityManager->getRepository(Game::class)->findBy([], ['date' => 'ASC']);

        // Don't consider games that have been played
        $upcomingGames = array_filter($upcomingGames, function ($game) {
            return $game->getHomeScore() === null && $game->getAwayScore() === null;
        });

        // Return only the next 3 upcoming games
        $upcomingGames = array_slice($upcomingGames, 0, 3);
        $result = [];
        foreach ($upcomingGames as $upcomingGame) {
            $result[] = [
                'date' => $upcomingGame->getDate()->format('Y-m-d H:i:s'),
                'location' => $upcomingGame->getLocation(),
                'homeTeam' => $upcomingGame->getHomeTeam()->getName(),
                'homeTeamLogo' => $upcomingGame->getHomeTeam()->getLogo(),
                'awayTeam' => $upcomingGame->getAwayTeam()->getName(),
                'awayTeamLogo' => $upcomingGame->getAwayTeam()->getLogo(),
                'homeOdds' => $upcomingGame->getHomeOdds(),
                'awayOdds' => $upcomingGame->getAwayOdds(),
                'overUnder' => $upcomingGame->getOverUnder(),
            ];
        }
        return new JsonResponse($result, 200);
    }
}
