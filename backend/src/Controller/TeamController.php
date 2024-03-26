<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NflTeamRepository; 


class TeamController extends AbstractController
{
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
}   
?>