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
    #[Route('/api/team', name: 'get_team_info', methods: ['GET'])]
    public function getTeamInfo(Request $request, NflTeamRepository $teamRepository, SerializerInterface $serializer): Response
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
}
?>