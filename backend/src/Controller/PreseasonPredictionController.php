<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PreseasonPrediction;
use App\Entity\User;
use App\Entity\NflTeam;
use App\Repository\PreseasonPredictionRepository;
use Error;

class PreseasonPredictionController extends AbstractController
{
    private $entityManager;
    private $preseasonPredictionRepository;

    public function __construct(EntityManagerInterface $entityManager, PreseasonPredictionRepository $preseasonPredictionRepository)
    {
        $this->entityManager = $entityManager;
        $this->preseasonPredictionRepository = $preseasonPredictionRepository;
    }

    private function convertPredictionToArray(PreseasonPrediction $prediction): array
    {
        return [
            'id' => $prediction->getId(),
            'user' => $prediction->getUser()->getUsername(),
            'afcChampion' => $prediction->getAFCChampion() ? $prediction->getAFCChampion()->getName() : null,
            'nfcChampion' => $prediction->getNFCChampion() ? $prediction->getNFCChampion()->getName() : null,
            'superBowlChampion' => $prediction->getSuperBowlWinner() ? $prediction->getSuperBowlWinner()->getName() : null,
            'mostPassingYards' => $prediction->getMostPassingYards() ? $prediction->getMostPassingYards()->getName() : null,
            'mostRushingYards' => $prediction->getMostRushingYards() ? $prediction->getMostRushingYards()->getName() : null,
            'firstPick' => $prediction->getFirstPick() ? $prediction->getFirstPick()->getName() : null,
            'mostPointsScored' => $prediction->getMostPointsScored() ? $prediction->getMostPointsScored()->getName() : null,
            'fewestPointsAllowed' => $prediction->getFewestPointsAllowed() ? $prediction->getFewestPointsAllowed()->getName() : null,
            'highestMarginOfVictory' => $prediction->getHighestMarginOfVictory() ? $prediction->getHighestMarginOfVictory()->getName() : null,
            'teamWithOROY' => $prediction->getOroy() ? $prediction->getOroy()->getName() : null,
            'teamWithDROY' => $prediction->getDroy() ? $prediction->getDroy()->getName() : null,
            'teamWithMVP' => $prediction->getMvp() ? $prediction->getMvp()->getName() : null,
        ];
    }

    private function getTeamByName($teamName)
    {
        return $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $teamName]);
    }

    #[Route('api/preseasonPrediction/{username}/fetch', name: 'fetch_preseasonPredictions', methods: ['GET'])]
    public function fetchPreseasonPredictions($username): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found!', 400]);
        }
        $preseasonPrediction = $this->preseasonPredictionRepository->findOneBy(['user' => $user]);
        if (!$preseasonPrediction) {
            return new JsonResponse(['data' => null]);
        }
        return new JsonResponse($this->convertPredictionToArray($preseasonPrediction), 200);
    }

    #[Route('api/preseasonPrediction/{username}/add', name: 'add_preseasonPredictions', methods: ['POST'])]
    public function addPreseasonPrediction(Request $request, $username): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found!', 400]);
        }
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            error_log($data);
            return new JsonResponse(['message' => 'Invalid data![No data provided!]'], 400);
        }
        $preseasonPrediction = $this->preseasonPredictionRepository->findOneBy(['user' => $user]);
        if (!$preseasonPrediction) {
            $preseasonPrediction = new PreseasonPrediction();
            $preseasonPrediction->setUser($user);
        }
        $data['afcChampion'] != '' ? $preseasonPrediction->setAFCChampion($this->getTeamByName($data['afcChampion'])) : $preseasonPrediction->setAFCChampion(null);
        $data['nfcChampion'] != '' ? $preseasonPrediction->setNFCChampion($this->getTeamByName($data['nfcChampion'])) : $preseasonPrediction->setNFCChampion(null);
        $data['superBowlChampion'] != '' ? $preseasonPrediction->setSuperBowlWinner($this->getTeamByName($data['superBowlChampion'])) : $preseasonPrediction->setSuperBowlWinner(null);
        $data['mostPassingYards'] != '' ? $preseasonPrediction->setMostPassingYards($this->getTeamByName($data['mostPassingYards'])) : $preseasonPrediction->setMostPassingYards(null);
        $data['mostRushingYards'] != '' ? $preseasonPrediction->setMostRushingYards($this->getTeamByName($data['mostRushingYards'])) : $preseasonPrediction->setMostRushingYards(null);
        $data['firstPick'] != '' ? $preseasonPrediction->setFirstPick($this->getTeamByName($data['firstPick'])) : $preseasonPrediction->setFirstPick(null);
        $data['mostPointsScored'] != '' ? $preseasonPrediction->setMostPointsScored($this->getTeamByName($data['mostPointsScored'])) : $preseasonPrediction->setMostPointsScored(null);
        $data['fewestPointsAllowed'] != '' ? $preseasonPrediction->setFewestPointsAllowed($this->getTeamByName($data['fewestPointsAllowed'])) : $preseasonPrediction->setFewestPointsAllowed(null);
        $data['highestMarginOfVictory'] != '' ? $preseasonPrediction->setHighestMarginOfVictory($this->getTeamByName($data['highestMarginOfVictory'])) : $preseasonPrediction->setHighestMarginOfVictory(null);
        $data['teamWithOROY'] != '' ? $preseasonPrediction->setOroy($this->getTeamByName($data['teamWithOROY'])) : $preseasonPrediction->setOroy(null);
        $data['teamWithDROY'] != '' ? $preseasonPrediction->setDroy($this->getTeamByName($data['teamWithDROY'])) : $preseasonPrediction->setDroy(null);
        $data['teamWithMVP'] != '' ? $preseasonPrediction->setMvp($this->getTeamByName($data['teamWithMVP'])) : $preseasonPrediction->setMvp(null);

        // initiate points with 0
        $preseasonPrediction->setAFCChampionPoints(0);
        $preseasonPrediction->setNFCChampionPoints(0);
        $preseasonPrediction->setSuperBowlWinnerPoints(0);
        $preseasonPrediction->setMostPassingYardsPoints(0);
        $preseasonPrediction->setMostRushingYardsPoints(0);
        $preseasonPrediction->setFirstPickPoints(0);
        $preseasonPrediction->setMostPointsScoredPoints(0);
        $preseasonPrediction->setFewestPointsAllowedPoints(0);
        $preseasonPrediction->setHighestMarginOfVictoryPoints(0);
        $preseasonPrediction->setOroyPoints(0);
        $preseasonPrediction->setDroyPoints(0);
        $preseasonPrediction->setMvpPoints(0);

        $this->entityManager->persist($preseasonPrediction);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Prediction added successfully!'], 200);
    }
}
