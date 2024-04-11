<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Game;

class ResultValidator {

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

        if (!$decodedData){
            return new JsonResponse(['message' => 'Invalid data![Data is not valid JSON!]'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($decodedData as $game) {
            if (!array_key_exists('homeTeamScore', $game) || !array_key_exists('awayTeamScore', $game)) {
                return new JsonResponse(['message' => 'Invalid data![HomeTeamScore or AwayteamScore not set!]'], Response::HTTP_BAD_REQUEST);
            }
        }
        return null;
    }
}

?>