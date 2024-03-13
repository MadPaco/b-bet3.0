<?php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatroomMessageRepository::class)]
class ChatroomMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $chatroomID;

    #[ORM\Column(type: 'integer')]
    private $senderID;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $sentAt;

    function getId(): ?int
    {
        return $this->id;
    }

    function getChatroomID(): ?int
    {
        return $this->chatroomID;
    }

    function setChatroomID(int $chatroomID): self
    {
        $this->chatroomID = $chatroomID;
        return $this;
    }

    function getSenderID(): ?int
    {
        return $this->senderID;
    }

    function setSenderID(int $senderID): self
    {
        $this->senderID = $senderID;
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

    function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    function setSentAt(DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

}

?>