<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\NflTeam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GameController extends AbstractController
{
    private $entityManager;
    private $request;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/game/fetchWeek', name: 'fetch_schedule', methods: ['GET'])]
    public function fetchSchedule(Request $request): Response
    {
        
        $weekNumber = $request->query->get('weekNumber');
        //fetch the games from a specific week is we have submitted a weekNumber, else fetch all games
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

    #[Route('/api/game/fetchGame', name: 'fetch_game', methods: ['GET'])]
    public function fetchGame(Request $request): Response
    {
        
        $gameID = $request->query->get('gameID');
        $game = $this->entityManager->getRepository(Game::class)->find($gameID);
        return $this->json($game);
    }

    #[Route('/api/game/editGame', name: 'edit_game', methods: ['POST'])]
    public function editGame(Request $request): Response
    {
        $gameID = $request->query->get('gameID');
        $game = $this->entityManager->getRepository(Game::class)->find($gameID);
        $data = json_decode($request->getContent(), true);
        $game->setWeekNumber($data['weekNumber']);
        $game->setDate(new \DateTime($data['date']));
        $game->setLocation($data['location']);

        $homeTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $data['homeTeam']]);
        $game->setHomeTeam($homeTeam);
        $awayTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $data['awayTeam']]);
        $game->setAwayTeam($awayTeam);

        $game->setHomeOdds($data['homeOdds']);
        $game->setAwayOdds($data['awayOdds']);
        $game->setOverUnder($data['overUnder']);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'all good, enjoy'], Response::HTTP_OK);
    }

    #[Route('/api/game/addGame', name: 'add_game', methods: ['POST'])]
    public function addGame(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $game = new Game();
        $game->setWeekNumber($data['weekNumber']);
        $game->setDate(new \DateTime($data['date']));
        $game->setLocation($data['location']);

        $homeTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $data['homeTeam']]);
        $game->setHomeTeam($homeTeam);
        $awayTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $data['awayTeam']]);
        $game->setAwayTeam($awayTeam);

        $game->setHomeOdds($data['homeOdds']);
        $game->setAwayOdds($data['awayOdds']);
        $game->setOverUnder($data['overUnder']);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'all good, enjoy'], Response::HTTP_OK);
    }
}


?>