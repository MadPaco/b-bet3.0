<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\AuthValidator;
use App\Entity\NflTeam;
use App\Entity\User;

class AuthController extends AbstractController
{
    private $validator;

    public function __construct(AuthValidator $validator)
    {
        $this->validator = $validator;
    }

    #[Route('/api/register', name: 'user_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder,
        JWTTokenManagerInterface $JWTManager,
    ): Response {
        $data = $request->request->all();
        $profilePicture = $request->files->get('profilePicture');

        // Validate non-file data
        $response = $this->validator->validateData($data);
        if ($response) {
            return $response;
        }

        // Create a new user entity
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordEncoder->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);
        $user->setUsername($data['username']);
        $user->setCreatedAt(new \DateTime());

        // Handle favorite team
        $teamRepository = $entityManager->getRepository(NflTeam::class);
        $favTeam = $teamRepository->findOneBy(['name' => $data['favTeam']]);
        if (!$favTeam) {
            return new JsonResponse(['message' => 'Favorite team not found!'], Response::HTTP_BAD_REQUEST);
        }
        $user->setFavTeam($favTeam);

        // Handle profile picture
        if ($profilePicture instanceof UploadedFile) {
            $newFilename = uniqid() . '.' . $profilePicture->guessExtension();
            try {
                $profilePicture->move(
                    $this->getParameter('profile_pictures_directory'), // directory path
                    $newFilename
                );
            } catch (FileException $e) {
                return new JsonResponse(['message' => 'Failed to upload profile picture'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $user->setProfilePicture($newFilename);
        }

        // Persist user
        $entityManager->persist($user);
        $entityManager->flush();

        // Generate JWT token
        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
