<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;


class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/backend/user', name: 'user', methods: ['GET'])]

    public function getUserInfo(Request $request): Response
    {
        $username = $request->query->get('username');
        $user = $this->userRepository->findOneBy(['username' => $username]);
    
        // If the user is not authenticated, return a 401 response
        if (!$user) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }
    
        // Get the user's favorite team
        $favTeam = $user->getFavTeam();
    
        // If the user does not have a favorite team, return a 404 response
        if (!$favTeam) {
            return new JsonResponse(['message' => 'Favorite team not found'], Response::HTTP_NOT_FOUND);
        }

        $createdAt = $user->getCreatedAt();
        if (!$createdAt) {
            return new JsonResponse(['message' => 'Created at not found'], Response::HTTP_NOT_FOUND);
        }
    
        // Return the user's favorite team, email, createdAt, username, and roles
        return new JsonResponse([
            'favTeam' => $favTeam->getName(),
            'email' => $user->getEmail(),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }
}