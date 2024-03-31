<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\NflTeam;
use App\Entity\Bet;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class BetController extends AbstractController
{
    private $entityManager;
    private $request;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/bet/addBets', name: 'add_bets', methods: ['POST'])]
    public function addBet(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        foreach ($data as $betData){

            $game = $this->entityManager->getRepository(Game::class)->find($betData['gameID']);
            $bet = $this->entityManager->getRepository(Bet::class)->findOneBy(['game' => $game, 'user' => $user]);  
            //there is no bet for this game and user yet, so create one
            if (!$bet) {
                $bet = new Bet();
                $bet->setGame($game);
                $bet->setUser($user);
                $bet->setPoints(0);
                $bet->setEditCount(0);
            }

            if ($bet->getHomePrediction() !== $betData['homePrediction'] || $bet->getAwayPrediction() !== $betData['awayPrediction']){
                $bet->setEditCount($bet->getEditCount() + 1);
            }
            
            //set the values in both cases if they differ from the old values
            if ($bet->getHomePrediction() !== $betData['homePrediction']) {
                $bet->setHomePrediction($betData['homePrediction']);
            }
            if ($bet->getAwayPrediction() !== $betData['awayPrediction']) {
                $bet->setAwayPrediction($betData['awayPrediction']);
            }

            $bet->setLastEdit(new \DateTime());

            
            $this->entityManager->persist($bet);
            $this->entityManager->flush();
        }
        return new JsonResponse(['message' => 'Bet added or updated!']);
    }

    #[Route('/api/bet/fetchBets', name: 'fetch_bets', methods: ['GET'])]
    public function fetchBets(Request $request): Response
    {
        $username = $request->query->get('user');
        $user = $this->entityManager->getRepository(User::Class)->findOneBy(['username' => $username]);
        $userID = $user ? $user->getId() : null;
        $weekNumber = $request->query->get('weekNumber');

        if ($weekNumber) {
            $bets = $this->entityManager->getRepository(Bet::Class)->findBetsByWeekNumber($weekNumber);
        }
        else {
            $bets = $this->entityManager->getRepository(Bet::Class)->findAll();
        }
        
        if ($userID){
            $bets = array_filter($bets, function($bet) use ($userID){
                return $bet->getUser()->getId() == $userID;
            });
            $bets = array_values($bets);
        }
        
        $betData = [];
        foreach ($bets as $bet) {
            $betData[] = [
                'gameID' => $bet->getGame()->getId(),
                'homePrediction' => $bet->getHomePrediction(),
                'awayPrediction' => $bet->getAwayPrediction(),
                'points' => $bet->getPoints(),
                'editCount' => $bet->getEditCount(),
                'lastEdit' => $bet->getLastEdit()->format('Y-m-d H:i:s')
            ];
        }
        return $this->json($betData);
    }

    // #[Route('/api/bet/editBet', name: 'edit_bet', methods: ['GET'])]

    // #[Route('/api/bet/deleteBet', name: 'delete_bet', methods: ['GET'])]



}

?>