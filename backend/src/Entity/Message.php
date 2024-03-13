<?php
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: MessageRepository::class)]

class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $senderID;

    #[ORM\Column(type: 'integer')]
    private $receiverID;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $sentAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReceiverID(): ?int
    {
        return $this->receiverID;
    }

    public function setReceiverID(int $receiverID): self
    {
        $this->receiverID = $receiverID;
        return $this;
    }

    public function getSenderID(): ?int
    {
        return $this->senderID;
    }

    public function setSenderID(int $senderID): self
    {
        $this->senderID = $senderID;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }
}

?>