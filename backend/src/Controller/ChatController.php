<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Chatroom;
use App\Entity\ChatroomMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class ChatController extends AbstractController
{
    private $entityManager;
    private $publisher;

    public function __construct(EntityManagerInterface $entityManager, PublisherInterface $publisher)
    {
        $this->entityManager = $entityManager;
        $this->publisher = $publisher;
    }
    #[Route("/api/chatroom/{id}", methods: ["GET"])]
    public function getChatroomMessages($id): Response
    {
        $chatroom = $this->entityManager->getRepository(Chatroom::class)->find($id);
    
        if (!$chatroom) {
            throw $this->createNotFoundException('Chatroom not found');
        }
    
        $messages = $this->entityManager->getRepository(ChatroomMessage::class)->findBy(['chatroom' => $chatroom]);
    
        $formattedMessages = array_map(function ($message) {
            return [
                'id' => $message->getId(),
                'chatroom' => $message->getChatroom()->getId(), // Assuming the chatroom has an ID
                'sender' => $message->getSender()->getUsername(), // Assuming the sender has an ID
                'content' => $message->getContent(),
                'sentAt' => $message->getSentAt(),
            ];
        }, $messages);
    
        return new JsonResponse($formattedMessages);
    }

    #[Route("/api/chatroom/{id}", methods: ["POST"])]
    public function postChatroomMessage(Request $request)
    {
        /* For now, I just implement a public chatroom with this
        Might want to add the possibility to create custom chatrooms later 
        down the road **/
        $chatroom = $this->entityManager->getRepository(Chatroom::class)->find(1);
        $user = $this->getUser();

        if (!$user) {
            return new Response('User not authenticated', 401);
        }

        if (!$chatroom) {
            throw $this->createNotFoundException('No chatroom found with id = 1');
        }

        $content = json_decode($request->getContent(), true)['content'];

        if ($content === null) {
            // Handle the case where content is null, e.g. throw an exception or return a response
            throw new \Exception('Content cannot be null');
        }

        // Sanitize the content
        $content = filter_var($content, FILTER_SANITIZE_STRING);

        $message = new ChatroomMessage();
        $message->setContent($content);
        $message->setSentAt(new \DateTime());
        $message->setChatroom($chatroom);
        $message->setSender($user);
    
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        //push the message to the chatroom vie websockets

        $update = new Update(
            "/api/chatroom/{$chatroom->getId()}", 
            json_encode(['content' => $content, 'sender' => $user->getUsername(), 'sentAt' => $message->getSentAt()]) // The data to send
        );
        $this->publisher->__invoke($update);
    
        return new Response('Message saved with id '.$message->getId());
    }
}

?>