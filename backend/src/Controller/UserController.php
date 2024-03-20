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

    #[Route('/backend/user', name: 'get_user', methods: ['GET'])]

    public function getUserInfo(Request $request): Response
    {
        $requestedUsername = $request->query->get('username');
        $requestedUser = $this->userRepository->findOneBy(['username' => $requestedUsername]);
    
        // If the user is not authenticated, return a 401 response
        if (!$requestedUser) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $authenticatedUser = $this->getUser();
    
        if (!$authenticatedUser) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        if ($authenticatedUser->getUsername() === $requestedUser->getUsername()) {
            return new JsonResponse([
                'favTeam' => $requestedUser->getFavTeam()->getName(),
                'email' => $requestedUser->getEmail(),
                'createdAt' => $requestedUser->getCreatedAt()->format('Y-m-d H:i:s'),
                'username' => $requestedUser->getUsername(),
                'roles' => $requestedUser->getRoles(),
            ]);
        }
        else {
            return new JsonResponse([
                'favTeam' => $requestedUser->getFavTeam()->getName(),
                'username' => $requestedUser->getUsername(),
                'roles' => $requestedUser->getRoles(),
            
            ]);
        }
    }

    #[Route('/backend/fetchUsers', name: 'get_all_user', methods: ['GET'])]
    public function getAllUsers(Request $request): Response{

        $authenticatedUser = $this->getUser();
    
        if (!$authenticatedUser) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $users = $this->userRepository->findAll();
        $userArray = [];
        foreach($users as $user){
            array_push($userArray, [
                'username' => $user->getUsername(),
                'favTeam' => $user->getFavTeam()->getName(),
            ]);
        }
        return new JsonResponse($userArray);
    }
}