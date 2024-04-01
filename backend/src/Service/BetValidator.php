<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Game;

class BetValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validateBetData($betData): ?JsonResponse
    {

        $requiredKeys = ['gameID', 'homePrediction', 'awayPrediction'];

        if (empty($betData) || !is_array($betData)) {
            return new JsonResponse(['message' => 'Invalid data![No data provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!array_key_exists('gameID', $betData) || !array_key_exists('homePrediction', $betData) || !array_key_exists('awayPrediction', $betData)) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!isset($betData['gameID']) || !isset($betData['homePrediction']) || !isset($betData['awayPrediction'])) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if ($betData['gameID'] === null || $betData['homePrediction'] === null || $betData['awayPrediction'] === null) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($betData)) {
            return new JsonResponse(['message' => 'Invalid data![No data provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!isset($betData['gameID']) || !isset($betData['homePrediction']) || !isset($betData['awayPrediction'])) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if ($betData['gameID'] === null || $betData['homePrediction'] === null || $betData['awayPrediction'] === null) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }

        if ($betData['homePrediction'] < 0 || $betData['awayPrediction'] < 0) {
            return new JsonResponse(['message' => 'Invalid data![away or home prediction are negative!]'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_int($betData['homePrediction']) || !is_int($betData['awayPrediction'])) {
            return new JsonResponse(['message' => 'Invalid data![away or home prediction are non-integers!]'], Response::HTTP_BAD_REQUEST);
        }

        if ($betData['gameID'] < 1 || !is_int($betData['gameID'])) {
            return new JsonResponse(['message' => 'Invalid data![gameID is < 1 or non-integer!]'], Response::HTTP_BAD_REQUEST);
        }

        $game = $this->entityManager->getRepository(Game::class)->find($betData['gameID']);
        if (!$game) {
            return new JsonResponse(['message' => 'Invalid data![game not found!]'], Response::HTTP_BAD_REQUEST);
        }
        return null;
    }

}
?>