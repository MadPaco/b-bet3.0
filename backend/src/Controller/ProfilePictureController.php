<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfilePictureController extends AbstractController
{
    #[Route('/profile-picture/{filename}', name: 'profile_picture')]
    public function getProfilePicture(string $filename): Response
    {

        $filePath = $this->getParameter('profile_pictures_directory') . '/' . $filename;
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found.');
        }

        return new BinaryFileResponse($filePath);
    }
}
?>