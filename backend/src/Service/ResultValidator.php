<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Game;

class ResultValidator
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    //data is sent to the api in this format:
    // {
    //     "1": {
    //         "homeTeamScore": 2,
    //         "awayTeamScore": 1
    //     },
    //     "2": {
    //         "homeTeamScore": 3,
    //         "awayTeamScore": 4
    //     }
    // }
    // etc, where the key is the game id

    public function validateData($data): ?JsonResponse
    {
        //check for missing/empty/null/incorrect formatted values
        if (empty($data)) {
            return new JsonResponse(['message' => 'Invalid data![No data provided!]'], Response::HTTP_BAD_REQUEST);
        }

        $decodedData = json_decode($data, true);

        if (!is_array($decodedData)) {
            return new JsonResponse(['message' => 'Invalid data![Data is not an array!]'], Response::HTTP_BAD_REQUEST);
        }

        if (!$decodedData) {
            return new JsonResponse(['message' => 'Invalid data![Data is not valid JSON!]'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($decodedData as $gameID => $game) {

            if (!is_numeric($gameID)) {
                return new JsonResponse(['message' => 'Invalid data![Missing or invalid gameID!]'], Response::HTTP_BAD_REQUEST);
            }

            if (!array_key_exists('homeTeamScore', $game) || !array_key_exists('awayTeamScore', $game)) {
                return new JsonResponse(['message' => 'Invalid data![HomeTeamScore or AwayteamScore not set!]'], Response::HTTP_BAD_REQUEST);
            }

            if (!is_numeric($game['homeTeamScore']) || !is_numeric($game['awayTeamScore'])) {
                return new JsonResponse(['message' => 'Invalid data![HomeTeamScore or AwayteamScore is not a number!]'], Response::HTTP_BAD_REQUEST);
            }

            if ($game['homeTeamScore'] < 0 || $game['awayTeamScore'] < 0) {
                return new JsonResponse(['message' => 'Invalid data![HomeTeamScore or AwayteamScore is negative!]'], Response::HTTP_BAD_REQUEST);
            }

            if (!is_int($game['homeTeamScore']) || !is_int($game['awayTeamScore'])) {
                return new JsonResponse(['message' => 'Invalid data![HomeTeamScore or AwayteamScore is not an integer!]'], Response::HTTP_BAD_REQUEST);
            }

            // teams never score more than 100 points, 
            // 1000+ is surely a typo
            if ($game['homeTeamScore'] >= 1000 || $game['awayTeamScore'] >= 1000) {
                return new JsonResponse(['message' => 'Invalid data![HomeTeamScore or AwayteamScore is too high!]'], Response::HTTP_BAD_REQUEST);
            }

            if (empty($decodedData)) {
                return new JsonResponse(['message' => 'Invalid data! [Missing gameID]'], Response::HTTP_BAD_REQUEST);
            }
        }
        return null;
    }
}
