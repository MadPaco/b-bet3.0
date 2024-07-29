<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Bet;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\BetValidator;
use App\Service\PredictionsAchievementChecker;

class BetController extends AbstractController
{
    private $entityManager;
    private $validator;
    private $predictionsAchievementChecker;

    public function __construct(EntityManagerInterface $entityManager, BetValidator $validator, PredictionsAchievementChecker $predictionsAchievementChecker)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->predictionsAchievementChecker = $predictionsAchievementChecker;
    }

    #[Route('/api/bet/addBets', name: 'add_bets', methods: ['POST'])]
    public function addBet(Request $request,): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (empty($data)) {
            return new JsonResponse(['message' => 'Invalid data![No data provided!]'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($data as $betData) {

            $validationResponse = $this->validator->validateBetData($betData);
            if ($validationResponse !== null) {
                return $validationResponse;
            }

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

            if (
                ($bet->getHomePrediction() !== $betData['homePrediction'] && $bet->getHomePrediction() !== null)
                || ($bet->getAwayPrediction() !== $betData['awayPrediction'] && $bet->getAwayPrediction() !== null)
            ) {
                $bet->setEditCount($bet->getEditCount() + 1);
                $bet->setPreviousHomePrediction($bet->getHomePrediction());
                $bet->setPreviousAwayPrediction($bet->getAwayPrediction());
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
        $this->predictionsAchievementChecker->checkAllAchievements($user);

        return new JsonResponse(['message' => 'Bet added or updated!']);
    }

    #[Route('/api/bet/fetchBets', name: 'fetch_bets', methods: ['GET'])]
    public function fetchBets(Request $request): Response
    {
        $username = $request->query->get('user');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        $userID = $user ? $user->getId() : null;
        $weekNumber = $request->query->get('weekNumber');

        if ($weekNumber) {
            $bets = $this->entityManager->getRepository(Bet::class)->findBetsByWeekNumber($weekNumber);
        } else {
            $bets = $this->entityManager->getRepository(Bet::class)->findAll();
        }

        if ($userID) {
            $bets = array_filter($bets, function ($bet) use ($userID) {
                return $bet->getUser()->getId() == $userID;
            });
            $bets = array_values($bets);
        }

        $betData = [];
        foreach ($bets as $bet) {
            $betData[] = [

                'username' => $bet->getUser()->getUsername(),
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
