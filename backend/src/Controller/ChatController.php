<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Reaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Chatroom;
use App\Entity\ChatroomMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class ChatController extends AbstractController
{
    private $entityManager;
    private $hub; 

    public function __construct(EntityManagerInterface $entityManager, HubInterface $hub)
    {
        $this->entityManager = $entityManager;
        $this->hub = $hub;
    }

    #[Route("/api/chatroom/", methods: ["GET"])]
    public function getChatroomMessages(Request $request): Response
    {
        if (!$request->query->get('chatroomID')){
            return new Response('Chatroom ID missing', 400);
        }
        
        $id = $request->query->get('chatroomID');

        if (!$id || !ctype_digit($id)){
            return new Response('Invalid Chatroom ID', 400);
        }


        $chatroom = $this->entityManager->getRepository(Chatroom::class)->find($id);
    
        if (!$chatroom) {
            return new Response ('Chatroom not found', 404);
        }
    
        $messages = $this->entityManager->getRepository(ChatroomMessage::class)->findBy(['chatroom' => $chatroom]);
    
        $formattedMessages = array_map(function ($message) {
            return [
                'id' => $message->getId(),
                'chatroom' => $message->getChatroom()->getId(),
                'sender' => $message->getSender()->getUsername(),
                'content' => $message->getContent(),
                'sentAt' => $message->getSentAt(),
                'reactions' => $message->getReactions()->map(function ($reaction) {
                    return [
                        'id' => $reaction->getId(),
                        'reactionCode' => $reaction->getReactionCode(),
                        'user' => $reaction->getUser()->getUsername(), // Assuming the user has an ID
                    ];
                })->toArray(),
            ];
        }, $messages);
    
        return new JsonResponse($formattedMessages);
    }

    #[Route("/api/chatroom/", methods: ["POST"])]
    public function postChatroomMessage(Request $request)
    {
        if (!$request->query->get('chatroomID')){
            return new Response('Chatroom ID missing', 400);
        }

        $id = $request->query->get('chatroomID');

        if (!$id || !ctype_digit($id)){
            return new Response('Invalid Chatroom ID', 400);
        }

        $chatroom = $this->entityManager->getRepository(Chatroom::class)->find($id);
        $user = $this->getUser();

        if (!$user) {
            return new Response('User not authenticated', 401);
        }

        if (!$chatroom) {
            return new Response('Chatroom not found', 404);
        }

        $data = $data = json_decode($request->getContent(), true);

        if (!isset($data['content'])){
            return new Response('Content missing', 400);
        }

        $content = json_decode($request->getContent(), true)['content'];
        if (!$content || $content === ''){
            return new Response('Content missing or empty', 400);
        }

        $sanitizedContent = htmlentities($content);
        $message = new ChatroomMessage();
        if ($sanitizedContent !== null && $sanitizedContent !== '') {
            $message->setContent($sanitizedContent);
        }
        else {
            throw new \Exception('Content cannot be empty');
        }
        $message->setSentAt(new \DateTime());
        $message->setChatroom($chatroom);
        $message->setSender($user);
    
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        //push the message to the chatroom vie websockets

        $update = new Update(
            "/chatroom/{$chatroom->getId()}", 
            json_encode(['content' => $sanitizedContent, 'sender' => $user->getUsername(), 'sentAt' => $message->getSentAt()]) // The data to send
        );
        $this->hub->publish($update);
    
        return new Response('Message saved with id '.$message->getId());
    }

    #[Route("/api/chatroom/{chatroomID}/message/{id}/reaction", methods: ["POST"])]
    public function addReaction(Request $request, $chatroomID, $id): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
    
        $chatroom = $this->entityManager->getRepository(Chatroom::class)->find($chatroomID);
        if (!isset($data['reaction'])){
            return new Response('Reaction missing', 400);
        }
    
        $reactionType = $data['reaction'];
        
        $message = $this->entityManager->getRepository(ChatroomMessage::class)->find($id);
        
        if (!$message) {
            return new Response('Message not found', 404);
        }
    
        // check if the user already reacted to the message
    
        $existingReaction = $this->entityManager->getRepository(Reaction::class)->findOneBy(['message' => $message, 'user' => $this->getUser()]);
        if ($existingReaction) {
            //If yes, remove the reaction
            $this->entityManager->remove($existingReaction);
            $this->entityManager->flush();
    
            $update = new Update(
                "/chatroom/{$chatroomID}",
                json_encode([
                    'id' => $message->getId(),
                    'status' => 'Reaction removed',
                    'reactions' => array_map(function($reaction) {
                        return $reaction->toArray();
                    }, $this->entityManager->getRepository(Reaction::class)->findBy(['message' => $message]))
            ]));
            $this->hub->publish($update);
    
            return new JsonResponse($message->getReactions());
        }

        else {
                    
            $reaction = new Reaction();
            $reaction->setReactionCode($reactionType);
            $reaction->setMessage($message);
            $reaction->setUser($this->getUser()); // Assuming the user is the one who is reacting
            
            $this->entityManager->persist($reaction);
            $this->entityManager->flush();
        
            // Publish the update to the Mercure hub
            $update = new Update(
                "/chatroom/{$chatroomID}",
                json_encode([
                    'id' => $message->getId(),
                    'status' => 'Reaction added',
                    'reactions' => array_map(function($reaction) {
                        return $reaction->toArray();
                    }, $this->entityManager->getRepository(Reaction::class)->findBy(['message' => $message]))
                ])
            );
            $this->hub->publish($update);
        
            return new JsonResponse($message->getReactions());

        }

    }

}

?>