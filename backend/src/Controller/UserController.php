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
    ) {
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
        if (!$requestedUsername) {
            return new JsonResponse(['message' => 'No username provided'], Response::HTTP_BAD_REQUEST);
        }
        $requestedUser = $this->userRepository->findOneBy(['username' => $requestedUsername]);
        if (!$requestedUser) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $authenticatedUser = $this->getUser();

        if (!$authenticatedUser) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (($authenticatedUser->getUsername() === $requestedUser->getUsername())
            || in_array('ADMIN', $authenticatedUser->getRoles())
        ) {
            return new JsonResponse([
                'favTeam' => $requestedUser->getFavTeam()->getName(),
                'email' => $requestedUser->getEmail(),
                'createdAt' => $requestedUser->getCreatedAt()->format('Y-m-d H:i:s'),
                'username' => $requestedUser->getUsername(),
                'roles' => $requestedUser->getRoles(),
                'profilePicture' => $requestedUser->getProfilePicture(),
                'bio' => $requestedUser->getBio(),
            ]);
        } else {
            return new JsonResponse([
                'favTeam' => $requestedUser->getFavTeam()->getName(),
                'username' => $requestedUser->getUsername(),
                'profilePicture' => $requestedUser->getProfilePicture(),
                'bio' => $requestedUser->getBio(),
            ]);
        }
    }

    #[Route('api/user/fetchAll', name: 'fetch_all_users', methods: ['GET'])]
    public function fetchAllUsers(Request $request): Response
    {

        $authenticatedUser = $this->getUser();
        $users = $this->userRepository->findAll();
        $userArray = [];
        foreach ($users as $user) {
            array_push($userArray, [
                'username' => $user->getUsername(),
                'favTeam' => $user->getFavTeam()->getName(),
                'profilePicture' => $user->getProfilePicture(),
                'bio' => $user->getBio(),
            ]);
        }
        return new JsonResponse($userArray);
    }

    #[Route('api/user/editUser', name: 'edit_user', methods: ['POST'])]
    public function editUser(Request $request): Response
    {
        $username = $request->query->get('username');
        if (!$username) {
            return new JsonResponse(['message' => 'No username provided'], Response::HTTP_BAD_REQUEST);
        }

        $userToChange = $this->userRepository->findOneBy(['username' => $username]);
        $authenticatedUser = $this->getUser();

        if (!$userToChange) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        if (!$authenticatedUser instanceof \App\Entity\User) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }
        if ($authenticatedUser->getUsername() !== $userToChange->getUsername() && !in_array('ADMIN', $authenticatedUser->getRoles())) {
            return new JsonResponse(['message' => 'Not authorized'], Response::HTTP_FORBIDDEN);
        }

        // Handle form data and file upload
        $newUsername = $request->request->get('username') ? filter_var($request->request->get('username'), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $newEmail = $request->request->get('email') ? filter_var($request->request->get('email'), FILTER_SANITIZE_EMAIL) : null;
        $newFavTeam = $request->request->get('favTeam') ? filter_var($request->request->get('favTeam'), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $newFavTeamID = $newFavTeam ? $this->nflTeamRepository->findOneBy(['name' => $newFavTeam]) : null;
        $newPassword = $request->request->get('password') ? filter_var($request->request->get('password'), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $newBio = $request->request->get('bio') ? filter_var($request->request->get('bio'), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $file = $request->files->get('profilePicture');

        // Check for unique username and email
        if ($newUsername && $newUsername !== $userToChange->getUsername()) {
            $existingUsername = $this->userRepository->findOneBy(['username' => $newUsername]);
            if ($existingUsername) {
                return new JsonResponse(['message' => 'Username already exists'], Response::HTTP_CONFLICT);
            }
        }
        if ($newEmail && $newEmail !== $userToChange->getEmail()) {
            $existingEmail = $this->userRepository->findOneBy(['email' => $newEmail]);
            if ($existingEmail) {
                return new JsonResponse(['message' => 'Email already exists'], Response::HTTP_CONFLICT);
            }
        }

        // Handle profile picture upload
        if ($file) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = $file->guessExtension();

            if (!in_array($extension, $allowedExtensions)) {
                return new JsonResponse(['message' => 'Invalid file type'], Response::HTTP_BAD_REQUEST);
            }

            $filename = uniqid() . '.' . $extension;
            try {
                $file->move($this->getParameter('profile_pictures_directory'), $filename);
            } catch (FileException $e) {
                return new JsonResponse(['message' => 'Failed to upload profile picture'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        // Update user entity
        try {
            if ($newUsername && $newUsername !== $userToChange->getUsername()) {
                $userToChange->setUsername($newUsername);
            }
            if ($newEmail && $newEmail !== $userToChange->getEmail()) {
                $userToChange->setEmail($newEmail);
            }
            if ($newFavTeamID && $newFavTeamID !== $userToChange->getFavTeam()) {
                $userToChange->setFavTeam($newFavTeamID);
            }
            if ($newPassword && $newPassword !== $userToChange->getPassword()) {
                $hashedPassword = $this->passwordEncoder->hashPassword($userToChange, $newPassword);
                $userToChange->setPassword($hashedPassword);
            }
            if ($newBio && $newBio !== $userToChange->getBio()) {
                $userToChange->setBio($newBio);
            }
            if (isset($filename)) {
                $userToChange->setProfilePicture($filename);
            }

            $this->entityManager->persist($userToChange);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'An error occurred while updating the user', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'User updated successfully'], Response::HTTP_OK);
    }



    #[Route('api/user/{username}/fetchFavTeamBanner', name: 'fetch_banner', methods: ['GET'])]
    public function fetchBanner($username): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);
        return new JsonResponse([
            'banner' => $user->getFavTeam()->getBanner(),
        ]);
    }
}
