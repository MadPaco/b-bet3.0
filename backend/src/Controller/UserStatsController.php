<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Bet;
use App\Entity\Game;
use App\Entity\NflTeam;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class UserStatsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function getPointsByWeek(User $user): array
    {
        $NFLWEEKS = $this->entityManager->getRepository(Game::class)->findLatestWeekWithResults();
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
        return $pointsPerWeek;
    }

    private function getAveragePointsPerWeek(User $user): array
    {
        // Get the points per week for the user
        $pointsPerWeek = $this->getPointsByWeek($user);
        $NFLWeeks = $this->entityManager->getRepository(Game::class)->findLatestWeekWithResults();
        $averagePointsPerWeek = [];
        $week = 1;
        while ($week  <= $NFLWeeks) {
            $averagePointsPerWeek[] = array_sum(array_slice($pointsPerWeek, 0, $week)) / ($week);
            $week++;
        }
        return $averagePointsPerWeek;
    }

    private function getHighestScoringWeek(User $user): int
    {
        $pointsPerWeek = $this->getPointsByWeek($user);
        return array_search(max($pointsPerWeek), $pointsPerWeek);
    }

    private function getLowestScoringWeek(User $user): int
    {
        $pointsPerWeek = $this->getPointsByWeek($user);
        return array_search(min($pointsPerWeek), $pointsPerWeek);
    }

    private function getTotalPoints(User $user): int
    {
        $pointsPerWeek = $this->getPointsByWeek($user);
        return array_sum($pointsPerWeek);
    }

    private function getTotalPointsUntilGivenWeek(User $user, int $week): int
    {
        $pointsPerWeek = $this->getPointsByWeek($user);
        return array_sum(array_slice($pointsPerWeek, 0, $week));
    }


    private function calculateLeaderboard(): array
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $leaderboard = [];
        foreach ($users as $user) {
            $leaderboard[] = [
                'username' => $user->getUsername(),
                'totalPoints' => $this->getTotalPoints($user),
            ];
        }
        usort($leaderboard, function ($a, $b) {
            return $b['totalPoints'] - $a['totalPoints'];
        });
        return $leaderboard;
    }

    private function calculateLeaderboardUntilGivenWeek(int $week): array
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $leaderboard = [];
        foreach ($users as $user) {
            $leaderboard[] = [
                'username' => $user->getUsername(),
                'totalPoints' => $this->getTotalPointsUntilGivenWeek($user, $week),
            ];
        }
        usort($leaderboard, function ($a, $b) {
            return $b['totalPoints'] - $a['totalPoints'];
        });
        return $leaderboard;
    }

    private function calculateLeaderboardEvolution(): array
    {
        $NFLWeeks = $this->entityManager->getRepository(Game::class)->findLatestWeekWithResults();
        $leaderboardCourse = [];
        for ($i = 1; $i <= $NFLWeeks; $i++) {
            $leaderboardCourse[$i] = $this->calculateLeaderboardUntilGivenWeek($i);
        }
        return $leaderboardCourse;
    }

    private function getPointDistribution(User $user): array
    {
        //get all games that are finished, indicated by homeScore and awayScore != null
        $finishedGames = $this->entityManager->getRepository(Game::class)->findFinishedGames();
        $pointDistribution = ['0' => 0, '1' => 0, '3' => 0, '5' => 0];
        foreach ($finishedGames as $game) {
            $bets = $this->entityManager->getRepository(Bet::class)->findBy(['game' => $game, 'user' => $user]);
            foreach ($bets as $bet) {
                $points = $bet->getPoints();
                $pointDistribution[$points]++;
            }
        }
        return $pointDistribution;
    }

    private function calculateHitRate(User $user): float
    // hit rate meaning a bet that has given points
    {
        $finishedGames = $this->entityManager->getRepository(Game::class)->findFinishedGames();
        $correctBets = 0;
        $totalBets = 0;
        foreach ($finishedGames as $game) {
            $bets = $this->entityManager->getRepository(Bet::class)->findBy(['game' => $game, 'user' => $user]);
            foreach ($bets as $bet) {
                $totalBets++;
                if ($bet->getPoints() > 0) {
                    $correctBets++;
                }
            }
        }
        if ($totalBets === 0) {
            return 0;
        }
        return $correctBets / $totalBets;
    }

    private function calculateHitRateUntilGivenWeek(User $user, int $week): float
    {
        $finishedGames = $this->entityManager->getRepository(Game::class)->findFinishedGames();
        $correctBets = 0;
        $totalBets = 0;
        foreach ($finishedGames as $game) {
            if ($game->getWeekNumber() <= $week) {
                $bets = $this->entityManager->getRepository(Bet::class)->findBy(['game' => $game, 'user' => $user]);
                foreach ($bets as $bet) {
                    $totalBets++;
                    if ($bet->getPoints() > 0) {
                        $correctBets++;
                    }
                }
            }
        }
        if ($totalBets === 0) {
            return 0;
        }
        return $correctBets / $totalBets;
    }

    private function calculateHitRateEvolution(User $user): array
    {
        $NFLWeeks = $this->entityManager->getRepository(Game::class)->findLatestWeekWithResults();
        $hitRateEvolution = [];
        for ($i = 1; $i <= $NFLWeeks; $i++) {
            $hitRateEvolution[$i] = $this->calculateHitRateUntilGivenWeek($user, $i);
        }
        return $hitRateEvolution;
    }

    private function calculateHitRateForGivenWeek(User $user, int $week): float
    {
        $finishedGames = $this->entityManager->getRepository(Game::class)->findFinishedGames();
        $correctBets = 0;
        $totalBets = 0;
        foreach ($finishedGames as $game) {
            if ($game->getWeekNumber() === $week) {
                $bets = $this->entityManager->getRepository(Bet::class)->findBy(['game' => $game, 'user' => $user]);
                foreach ($bets as $bet) {
                    $totalBets++;
                    if ($bet->getPoints() > 0) {
                        $correctBets++;
                    }
                }
            }
        }
        if ($totalBets === 0) {
            return 0;
        }
        return $correctBets / $totalBets;
    }

    private function calculateHitRateForEachWeek(User $user): array
    {
        $NFLWeeks = $this->entityManager->getRepository(Game::class)->findLatestWeekWithResults();
        $hitRatePerWeek = [];
        for ($i = 1; $i <= $NFLWeeks; $i++) {
            $hitRatePerWeek[$i] = $this->calculateHitRateForGivenWeek($user, $i);
        }
        return $hitRatePerWeek;
    }

    private function calculateTeamHitRate(User $user, NflTeam $team): float
    {
        // returns the rate 
        $teamHits = 0;
        $totalGames = 0;
        $finishedGames = $this->entityManager->getRepository(Game::class)->findFinishedGames();
        foreach ($finishedGames as $game) {
            if ($game->getHomeTeam() === $team || $game->getAwayTeam() === $team) {
                $bet = $this->entityManager->getRepository(Bet::class)->findOneBy(['game' => $game, 'user' => $user]);
                if ($bet && $bet->getPoints() > 0) {
                    $teamHits++;
                    $totalGames++;
                } else {
                    $totalGames++;
                }
            }
        }
        return $teamHits / $totalGames;
    }

    private function calculateAllTeamHitRates(User $user): array
    {
        $teams = $this->entityManager->getRepository(NflTeam::class)->findAll();
        $teamHitRates = [];
        foreach ($teams as $team) {
            $teamHitRates[$team->getName()] = $this->calculateTeamHitRate($user, $team);
        }
        arsort($teamHitRates); //sort descending for the frontend
        return $teamHitRates;
    }

    private function calculateTeamAveragePoints(User $user, NflTeam $team): float
    {
        // returns average points for games where the given team participated
        $pointSum = 0;
        $totalGames = 0;
        $finishedGames = $this->entityManager->getRepository(Game::class)->findFinishedGames();
        foreach ($finishedGames as $game) {
            if ($game->getHomeTeam() === $team || $game->getAwayTeam() === $team) {
                $bet = $this->entityManager->getRepository(Bet::class)->findOneBy(['game' => $game, 'user' => $user]);
                if ($bet && $bet->getPoints() > 0) {
                    $totalGames++;
                    $pointSum += $bet->getPoints();
                } else {
                    $totalGames++;
                }
            }
        }
        return $pointSum / $totalGames;
    }

    private function calculateAllTeamAveragePoints(User $user): array
    {
        $teams = $this->entityManager->getRepository(NflTeam::class)->findAll();
        $teamAveragePoints = [];
        foreach ($teams as $team) {
            $teamAveragePoints[$team->getName()] = $this->calculateTeamAveragePoints($user, $team);
        }
        //sort descending for the frontend
        arsort($teamAveragePoints);
        return $teamAveragePoints;
    }


    #[Route('api/stats/userStats/{username}', name: 'fetch_user_stats', methods: ['GET'])]
    public function fetchUserStats(string $username): JsonResponse
    {
        // Fetch the user by username
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Count the number of bets placed by the user
        $betsPlaced = $this->entityManager->getRepository(Bet::class)->count(['user' => $user]);

        // Get the points per week for the user
        $pointsPerWeek = $this->getPointsByWeek($user);

        // Return the collection of stats
        return new JsonResponse([
            'betsPlaced' => $betsPlaced,
            'pointsPerWeek' => $this->getPointsByWeek($user),
            'totalPoints' => $this->getTotalPoints($user),
            'currentPlace' => array_search($user->getUsername(), array_column($this->calculateLeaderboard(), 'username')) + 1,
            'pointDistribution' => $this->getPointDistribution($user),
            'highestScoringWeek' => $this->getHighestScoringWeek($user),
            'lowestScoringWeek' => $this->getLowestScoringWeek($user),
            'latestWeek' => $this->entityManager->getRepository(Game::class)->findLatestWeekWithResults(),
            'averagePointsPerWeek' => $this->getAveragePointsPerWeek($user),
            'hitRate' => $this->calculateHitRate($user),
            'teamHitRate' => $this->calculateAllTeamHitRates($user),
            'teamPointAverage' => $this->calculateAllTeamAveragePoints($user),
            'leaderboardEvolution' => $this->calculateLeaderboardEvolution(),
            'hitRateEvolution' => $this->calculateHitRateEvolution($user),
            'hitRatePerWeek' => $this->calculateHitRateForEachWeek($user),
        ], 200);
    }

    #[Route('/api/stats/userStats/{username}/short', name: 'fetch_user_stats_short', methods: ['GET'])]
    public function fetchShortStats(string $username): JsonResponse
    {
        // this method is used to fetch the user stats for the userinfopanel
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        return new JsonResponse([
            'totalPoints' => $this->getTotalPoints($user),
            'currentPlace' => array_search($user->getUsername(), array_column($this->calculateLeaderboard(), 'username')) + 1,
            'hitRate' => $this->calculateHitrate($user),
            'betsPlaced' => $this->entityManager->getRepository(Bet::class)->count(['user' => $user]),
            'highestScoringWeek' => $this->getHighestScoringWeek($user),
            'lowestScoringWeek' => $this->getLowestScoringWeek($user),
        ], 200);
    }

    #[Route('api/stats/leaderboard', name: 'fetch_leaderboard', methods: ['GET'])]
    public function fetchLeaderboard(): JsonResponse
    {
        $leaderboard = $this->calculateLeaderboard();
        return new JsonResponse($leaderboard, 200);
    }
}
