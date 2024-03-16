<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Chatroom;
use App\Entity\User;

#[ORM\Entity(repositoryClass: 'App\Repository\ChatroomMessageRepository')]
class ChatroomMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Chatroom::class)]
    #[ORM\JoinColumn(name: "chatroomID", referencedColumnName: "id")]
    private $chatroom;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "senderID", referencedColumnName: "id")]
    private $sender;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $sentAt;

    function getId(): ?int
    {
        return $this->id;
    }

    public function getChatroom(): ?Chatroom
    {
        return $this->chatroom;
    }

    public function setChatroom(?Chatroom $chatroom): self
    {
        $this->chatroom = $chatroom;
        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    function getContent(): ?string
    {
        return $this->content;
    }

    function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

}

?>