<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class UserController extends AbstractController
{
    private $userRepository;
    private $entityManager;
    private $passwordEncoder;
    private $tokenManager;

    public function __construct(
        UserRepository $userRepository, 
        UserPasswordHasherInterface $passwordEncoder, 
        EntityManagerInterface $entityManager, 
        JWTTokenManagerInterface $tokenManager
        )
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }

    #[Route('api/user/getUser', name: 'get_user', methods: ['GET'])]

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

    #[Route('/api/user/fetchAll', name: 'get_all_user', methods: ['GET'])]
    public function getAllUsers(Request $request): Response{

        $authenticatedUser = $this->getUser();
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

    #[Route('api/user/edit', name: 'edit_user', methods: ['POST'])]
    public function editUser(Request $request): Response
    {
        $authenticatedUser = $this->getUser();
    
        if (!$authenticatedUser) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        
        $newUsername = filter_var($data['username'], FILTER_SANITIZE_STRING);
        $newEmail = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $newFavTeam = filter_var($data['favTeam'], FILTER_SANITIZE_STRING);
        $newPassword = filter_var($data['password'], FILTER_SANITIZE_STRING);

        $exisitingUsername = $this->userRepository->findOneBy(['username' => $newUsername]);
        if($exisitingUsername){
            return new JsonResponse(['message' => 'Username already exists'], Response::HTTP_CONFLICT);
        }

        $newEmail = $data['email'];
        $existingEmail = $this->userRepository->findOneBy(['email' => $newEmail]);
        if($existingEmail){
            return new JsonResponse(['message' => 'Email already exists'], Response::HTTP_CONFLICT);
        }


        try {
            if ($newUsername != null){
                $authenticatedUser->setUsername($newUsername);
            }
            if ($newEmail != null){
                $authenticatedUser->setEmail($newEmail);
            }
            if ($newFavTeam != null){
                $authenticatedUser->setFavTeam($newFavTeam);
            }
            if ($newPassword != null){
                $hashedPassword = $this->passwordEncoder->encodePassword($authenticatedUser, $newPassword);
                $authenticatedUser->setPassword($hashedPassword);
            }

            $this->entityManager->persist($authenticatedUser);
            $this->entityManager->flush();

        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'An error occurred while updating the user', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return new JsonResponse(['message' => 'all good, enjoy'], Response::HTTP_OK);
    }
}