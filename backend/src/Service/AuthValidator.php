<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\NflTeam;

class AuthValidator {

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    //data includes:
    //email, username, password, favTeam
    public function validateData($data): ?JsonResponse
    {

        //check for missing/empty/null/incorrect formatted values
        $requiredKeys = ['email', 'username', 'password', 'favTeam'];

        if (empty($data) || !is_array($data)) {
            return new JsonResponse(['message' => 'Invalid data![No data provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!array_key_exists('email', $data) || !array_key_exists('username', $data) || !array_key_exists('password', $data) || !array_key_exists('favTeam', $data)) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!isset($data['email']) || !isset($data['username']) || !isset($data['password']) || !isset($data['favTeam'])) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }
    
        if ($data['email'] === null || $data['username'] === null || $data['password'] === null || $data['favTeam'] === null) {
            return new JsonResponse(['message' => 'Invalid data![Not all parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($data['email']) || empty($data['username']) || empty($data['password']) || empty($data['favTeam'])) {
            return new JsonResponse(['message' => 'Invalid data![Empty parameters provided!]'], Response::HTTP_BAD_REQUEST);
        }

        // check for short passwords, invalid emails, and non-string usernames/passwords
        if (strlen($data['password']) < 8) {
            return new JsonResponse(['message' =>'Invalid Password![Password < 8 characters!]'], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['message' => 'Invalid Email![Email is not valid!]'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_string($data['username'])) {
            return new JsonResponse(['message' => 'Invalid Username![Username is not a string!]'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_string($data['password'])) {
            return new JsonResponse(['message' => 'Invalid Password![Password is not a string!]'], Response::HTTP_BAD_REQUEST);
        }

        //check for existing email, username
        $existingEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingEmail) {
            return new JsonResponse(['message' => 'Email already exists!'], Response::HTTP_BAD_REQUEST);
        }

        $existinUsername = $this->entityManager->getRepository(User::class)->findOneBy(['username'=> $data['username']]);
        if ($existinUsername) {
            return new JsonResponse(['message' => 'Username already exists!'], Response::HTTP_BAD_REQUEST);
        }

        //check for existing team
        $existingTeam = $this->entityManager->getRepository(NflTeam::class)->findOneBy(['name' => $data['favTeam']]);
        if (!$existingTeam) {
            return new JsonResponse(['message' => 'Team does not exist!'], Response::HTTP_BAD_REQUEST);
        }
        
        //check for wrong content type
        

        return null;
    }
}

?>