<?php

namespace App\Controller;

use App\Entity\NflTeam;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends AbstractController
{

#[Route('/backend/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, JWTTokenManagerInterface $JWTManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordEncoder->hashPassword($user, $data['password']));
        $user->setRoles(['USER']);
        $user->setUsername($data['username']);
        $user->setCreatedAt(new \DateTime());
        $teamName = $data['favTeam'];
        $teamRepository = $entityManager->getRepository(NflTeam::class);
        $favTeam = $teamRepository->findOneBy(['name' => $teamName]);

        if (!$favTeam) {
            throw $this->createNotFoundException(
                'No team found for name '.$teamName
            );
        }

        $user->setFavTeam($favTeam);
        $token = $JWTManager->create($user);
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(['token' => $token]);
    }

    #[Route('/backend/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, JWTTokenManagerInterface $JWTManager): Response
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['message' => 'Invalid JSON']);
        }
    
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $data['email']]);
    
        // If no user is found by email, try to find a user by username
        if (!$user) {
            $user = $userRepository->findOneBy(['username' => $data['email']]);
        }
    
        if (!$user) {
            return new JsonResponse(['message' => 'User not found']);
        }
    
        if (!$passwordEncoder->isPasswordValid($user, $data['password'])) {
            return new Response('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }
    
        $token = $JWTManager->create($user);
    
        return new JsonResponse(['token' => $token]);
    }
}
    ?>