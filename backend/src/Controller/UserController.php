<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\NflTeam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use App\Repository\NflTeamRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class UserController extends AbstractController
{
    private $userRepository;
    private $nflTeamRepository;
    private $entityManager;
    private $passwordEncoder;
    private $tokenManager;

    public function __construct(
        UserRepository $userRepository, 
        NflTeamRepository $nflTeamRepository,
        UserPasswordHasherInterface $passwordEncoder, 
        EntityManagerInterface $entityManager, 
        JWTTokenManagerInterface $tokenManager
        )
    {
        $this->userRepository = $userRepository;
        $this->nflTeamRepository = $nflTeamRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }

    #[Route('api/user/fetchUser', name: 'fetch_user', methods: ['GET'])]

    public function fetchUserInfo(Request $request): Response
    {
        $requestedUsername = $request->query->get('username');
        $requestedUser = $this->userRepository->findOneBy(['username' => $requestedUsername]);

        $authenticatedUser = $this->getUser();
    
        if (!$authenticatedUser) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (($authenticatedUser->getUsername() === $requestedUser->getUsername()) 
        || in_array('ADMIN', $authenticatedUser->getRoles())) {
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

    #[Route('api/user/fetchAll', name: 'fetch_all_users', methods: ['GET'])]
    public function fetchAllUsers(Request $request): Response{

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

    #[Route('api/user/editUser', name: 'edit_user', methods: ['POST'])]
    public function editUser(Request $request): Response
    {
        $userToChange = $this->userRepository->findOneBy(['username' => $request->query->get('username')]);
        $authenticatedUser = $this->getUser();
        //some guard clauses
        if (!$userToChange){
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        if (!$authenticatedUser){
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }
        //only allow to change a users info if the user is doing it themself or if the user is an admin
        if ($authenticatedUser->getUsername() !== $userToChange->getUsername() && !in_array('ADMIN', $authenticatedUser->getRoles())){
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        
        //sanitize 
        $newUsername = isset($data['username']) ? filter_var($data['username'], FILTER_SANITIZE_STRING) : null;
        $newEmail = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $newFavTeam = isset($data['favTeam']) ? filter_var($data['favTeam'], FILTER_SANITIZE_STRING) : null;
        $newFavTeamID = $newFavTeam ? $this->nflTeamRepository->findOneBy(['name' => $newFavTeam]) : null;
        $newPassword = isset($data['password']) ? filter_var($data['password'], FILTER_SANITIZE_STRING) : null;

        // keep username and email unqiue
        $exisitingUsername = $this->userRepository->findOneBy(['username' => $newUsername]);
        if($exisitingUsername){
            return new JsonResponse(['message' => 'Username already exists'], Response::HTTP_CONFLICT);
        }
        $existingEmail = $this->userRepository->findOneBy(['email' => $newEmail]);
        if($existingEmail){
            return new JsonResponse(['message' => 'Email already exists'], Response::HTTP_CONFLICT);
        }

        //set values and update the user, but only if the values differ from saved values
        try {
            if ($newUsername != null && $newUsername !== $userToChange->getUsername()){
                $userToChange->setUsername($newUsername);
            }
            if ($newEmail != null && $newEmail !== $userToChange->getEmail()){
                $userToChange->setEmail($newEmail);
            }
            if ($newFavTeamID != null && $newFavTeamID !== $userToChange->getFavTeam()){
                $userToChange->setFavTeam($newFavTeamID);
            }
            if ($newPassword != null && $newPassword !== $userToChange->getPassword()){
                $hashedPassword = $this->passwordEncoder->hashPassword($userToChange, $newPassword);
                $userToChange->setPassword($hashedPassword);
            }

            $this->entityManager->persist($userToChange);
            $this->entityManager->flush();

        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'An error occurred while updating the user', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return new JsonResponse(['message' => 'all good, enjoy'], Response::HTTP_OK);
    }
}