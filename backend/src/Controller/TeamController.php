<?php
namespace App\Controller;

use App\Entity\NflTeam;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\NflTeamRepository; 
use App\Repository\GameRepository;


class TeamController extends AbstractController
{
    private $entityManager;
    private $gameRepository;

    public function __construct(EntityManagerInterface $entityManager, GameRepository $gameRepository)
    {
        $this->entityManager = $entityManager;
        $this->gameRepository = $gameRepository;
    }

    #[Route('/api/team/fetchTeaminfo/', name: 'fetch_team_info', methods: ['GET'])]
    public function getTeamInfo(Request $request, NflTeamRepository $teamRepository): Response
    {
        $favTeam = $request->query->get('favTeam');

        if (!$favTeam) {
            return $this->json(['error' => 'favTeam parameter is missing'], 400);
        }

        $team = $teamRepository->findOneBy(['name' => $favTeam]);

        if (!$team) {
            return $this->json(['error' => 'Team not found'], 404);
        }

        return $this->json($team, 200);
    }

    #[Route('api/team/fetchAllTeamNames', name: 'fetch_all_team_names', methods: ['GET'])]
    public function getAllTeamNames(Request $request, NflTeamRepository $teamRepository): Response
    {
        $nflTeamsCollection = $teamRepository->findAll();
        $teamNames = array_map(function($nflTeam){
            return $nflTeam->getName();
        }, $nflTeamsCollection);
        return $this->json($teamNames, 200);
    }

    #[Route('api/team/fetchTeamStats/{teamName}/', name: 'fetch_team_stats', methods: ['GET'])]
    public function getTeamStats(Request $request, NflTeamRepository $teamRepository, $teamName): Response
    {
        if (!$teamName) {
            return $this->json(['error' => 'team parameter is missing'], 400);
        }

        $team = $teamRepository->findOneBy(['name' => $teamName]);

        if (!$team) {
            return $this->json(['error' => 'Team not found'], 404);
        }
        $wins = $team->getWins();
        $losses = $team->getLosses();
        $ties = $team->getTies();
        $pointsFor = $team->getPointsFor();
        $pointsAgainst = $team->getPointsAgainst();
        $netPoints = $team->getNetPoints();
        $stats = [
            'wins' => $wins,
            'losses' => $losses,
            'ties' => $ties,
            'pointsFor' => $pointsFor,
            'pointsAgainst' => $pointsAgainst,
            'netPoints' => $netPoints
        ];
        return $this->json($stats, 200);
    }

    public function calculateWinPercentage($team){
        //ties are counted as half a win for both teams
        //those half wins are stores in tieWins
        $gamesPlayed = $team->getWins() + $team->getLosses() + $team->getTies();
        if ($team->getTies() > 0){
            $tieWins += $team->getTies()/2;
        }
        if ($gamesPlayed == 0){
            return 0;
        }
        return ($team->getWins()+$tieWins)/$gamesPlayed;
    }

    private function calculateGroupWinPercentage($team, $allTeams)
    {
        $totalGames = 0;
        $totalWins = 0;

        foreach ($allTeams as $opponent) {
            if ($team['name'] !== $opponent['name']) {
                $record = $this->getHeadToHeadWinner($team['name'], $opponent['name']);
                $totalGames += 1; 
                if ($record === $team['name']) {
                    $totalWins += 1;
                }
            }
        }

        return $totalGames > 0 ? $totalWins / $totalGames : 0;
    }

    public function orderByWinpercentage(&$standings){
        usort($standings, function($a, $b) {
            if ($a['winPercentage'] == $b['winPercentage']) {
                return 0;
            }
            return ($a['winPercentage'] < $b['winPercentage']) ? 1 : -1;
        });
    }

    public function detectWinpercentageTie($standings){
        $winPercentages = array_map(function($team){
            return $team['winPercentage'];
        }, $standings);
        $uniqueWinPercentages = array_unique($winPercentages);
        return count($winPercentages) != count($uniqueWinPercentages);
    }

    private function getTeamsWithSameWinPercentage($standings)
    {
        $teamsGroupedByWinPercentage = [];
        $previousWinPercentage = null;
        $currentGroup = [];

        foreach ($standings as $team) {
            if ($previousWinPercentage === null || $team['winPercentage'] == $previousWinPercentage) {
                $currentGroup[] = $team;
            } else {
                if (count($currentGroup) >= 2) {
                    $teamsGroupedByWinPercentage[] = $currentGroup;
                }
                $currentGroup = [$team];
            }
            $previousWinPercentage = $team['winPercentage'];
        }

      
        $teamsGroupedByWinPercentage[] = $currentGroup;

        return $teamsGroupedByWinPercentage;
    }

    private function getHeadToHeadWinner($team1Name, $team2Name)
    {
        $team1Entity = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $team1Name]);
        $team2Entity = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $team2Name]);

        $game1 = $this->entityManager->getRepository(Game::class)->findOneBy(['homeTeam' => $team1Entity, 'awayTeam' => $team2Entity]);
        $game2 = $this->entityManager->getRepository(Game::class)->findOneBy(['homeTeam' => $team2Entity, 'awayTeam' => $team1Entity]);
        $team1Wins = 0;
        $team2Wins = 0;
        
        if ($game1->getHomeScore() !== null && $game1->getAwayScore() !== null) {
            if ($game1->getHomeScore() > $game1->getAwayScore()) {
                $team1Wins++;
            } elseif ($game1->getHomeScore() < $game1->getAwayScore()) {
                $team2Wins++;
            }
        }
    
        if ($game2->getHomeScore() !== null && $game2->getAwayScore() !== null) {
            if ($game2->getHomeScore() < $game2->getAwayScore()) {
                $team1Wins++;
            } elseif ($game2->getHomeScore() > $game2->getAwayScore()) {
                $team2Wins++;
            }
        }
    
        if ($team1Wins > $team2Wins) {
            return $team1Name;
        } elseif ($team2Wins > $team1Wins) {
            return $team2Name;
        } else {
            return null;
        }
    }

    private function resolveMultiTeamTie($teams)
    {
        foreach ($teams as &$team) {
            $team['headToHeadWinPercentage'] = $this->calculateGroupWinPercentage($team, $teams);
        }

        usort($teams, function ($teamA, $teamB) {
            return $teamB['headToHeadWinPercentage'] <=> $teamA['headToHeadWinPercentage'];
        });

        return $teams;
    }

    private function orderAfterHeadtoHead($standings)
    {
        $teamsWithSameWinPercentage = $this->getTeamsWithSameWinPercentage($standings);
        $updatedStandings = [];
        
        foreach ($teamsWithSameWinPercentage as $group) {
            if (count($group) == 2) {
                $team1 = $group[0];
                $team2 = $group[1];
    
                $headToHeadWinner = $this->getHeadToHeadWinner($team1['name'], $team2['name']);
                if ($headToHeadWinner !== null) {
                    if ($headToHeadWinner == $team1['name']) {
                        $updatedStandings[] = $team1;
                        $updatedStandings[] = $team2;
                    } else {
                        $updatedStandings[] = $team2;
                        $updatedStandings[] = $team1;
                    }
                } else {
                    // Append the group as is if head-to-head doesn't break the tie
                    $updatedStandings = array_merge($updatedStandings, $group);
                }
            } else {
                // More than 2 teams, resolve multi-team tie
                $resolvedGroup = $this->resolveMultiTeamTie($group);
                $updatedStandings = array_merge($updatedStandings, $resolvedGroup);
            }
        }
    
        // Add any teams not in the updated standings yet
        foreach ($standings as $team) {
            if (!in_array($team, $updatedStandings, true)) {
                $updatedStandings[] = $team;
            }
        }
    
        return $updatedStandings;
    }

    private function calculateDivisionWinpercentage(NflTeam $team): float
    {
        // Fetch all games played by the team versus division opponents
        $games = $this->gameRepository->findDivisionGamesForTeam($team);
    
        // Filter out games that have not been played yet
        $games = array_filter($games, function ($game) {
            return $game->getHomeScore() !== null && $game->getAwayScore() !== null;
        });
    
        // Calculate win percentage in those games
        $wins = 0;
        $losses = 0;
        $ties = 0;
    
        foreach ($games as $game) {
            if ($game->getHomeTeam() === $team) {
                if ($game->getHomeScore() > $game->getAwayScore()) {
                    $wins++;
                } elseif ($game->getHomeScore() < $game->getAwayScore()) {
                    $losses++;
                } else {
                    $ties++;
                }
            } else {
                if ($game->getHomeScore() < $game->getAwayScore()) {
                    $wins++;
                } elseif ($game->getHomeScore() > $game->getAwayScore()) {
                    $losses++;
                } else {
                    $ties++;
                }
            }
        }
    
        // Check if there are any games to avoid division by zero
        $gamesCount = count($games);
        if ($gamesCount === 0) {
            return 0;
        }
    
        return ($wins + $ties / 2) / $gamesCount;
    }

    private function orderAfterDivisionWinpercentage(array $standings): array
    {
        // Group teams by the same win percentage
        $teamsGroupedByWinPercentage = $this->getTeamsWithSameWinPercentage($standings);
        $updatedStandings = [];
    
        foreach ($teamsGroupedByWinPercentage as $group) {
            // Only process groups with two or more teams
            if (count($group) >= 2) {
                // Sort the group by division win percentage
                usort($group, function ($team1, $team2) {
                    $team1Entity = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $team1['name']]);
                    $team2Entity = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $team2['name']]);
                    
                    $team1WinpercentageInDivision = $this->calculateDivisionWinpercentage($team1Entity);
                    $team2WinpercentageInDivision = $this->calculateDivisionWinpercentage($team2Entity);
    
                    // Compare division win percentages
                    return $team2WinpercentageInDivision <=> $team1WinpercentageInDivision;
                });
    
                // Append the sorted group to updated standings
                $updatedStandings = array_merge($updatedStandings, $group);
            } else {
                // Append groups with only one team directly to the updated standings
                $updatedStandings = array_merge($updatedStandings, $group);
            }
        }
    
        return $updatedStandings;
    }


    #[Route('/api/team/fetchDivisionStandings/{conference}/{division}', name: 'fetch_division_standings', methods: ['GET'])]
    public function fetchDivisionStandings(Request $request, $conference, $division): Response
    //https://www.nfl.com/standings/tie-breaking-procedures
    //I will implement this up to (including) point 2, 
    //this is just a minor feature and I don't want to spend too much time on it
    //Might implement the rest down the road
    {
        $teams = $this->entityManager->getRepository(NflTeam::class)->findBy(['conference' => $conference, 'division' => $division]);
    
        $standings = array_map(function ($team) {
            return [
                'name' => $team->getName(),
                'winPercentage' => $this->calculateWinPercentage($team),
                'wins' => $team->getWins(),
                'losses' => $team->getLosses(),
                'ties' => $team->getTies(),
                'pointsFor' => $team->getPointsFor(),
                'pointsAgainst' => $team->getPointsAgainst(),
                'netPoints' => $team->getNetPoints()
            ];
        }, $teams);
    
        $this->orderByWinpercentage($standings);
    
        if (!$this->detectWinpercentageTie($standings)) {
            return $this->json($standings, 200);
        }
        // first tie breakere, head to head record
        // if orderAfterHeadtoHead returns null, head to head didn't break the tie
        if (!$this->orderAfterHeadtoHead($standings) === null) {
            return new JsonResponse('Head to head did not break the tie', 200);
            //return $this->json($this->orderAfterHeadtoHead($standings), 200);
        }
        $standings = $this->orderAfterHeadtoHead($standings);
        //return only the first four entries
        return new JsonResponse(array_slice($standings, 0, 4), 200);
        //$standings = $this->orderAfterDivisionWinpercentage($standings);
        //return new JsonResponse($standings, 200);

    }
}   
?>