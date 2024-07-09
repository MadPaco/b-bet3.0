<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Chatroom;
use App\Entity\User;
use App\Entity\Reaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: 'App\Repository\ChatroomMessageRepository')]
class ChatroomMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Chatroom::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: "chatroomID", referencedColumnName: "id")]
    private $chatroom;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "chatroomMessages")]
    #[ORM\JoinColumn(name: "senderID", referencedColumnName: "id")]
    private $sender;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $sentAt;

    #[ORM\OneToMany(targetEntity: Reaction::class, mappedBy: "message", fetch: "EAGER")]
    private $reactions;

    public function __construct()
    {
        $this->reactions = new ArrayCollection();
    }

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

    public function getReactions(): Collection
    {
        if ($this->reactions === null) {
            $this->reactions = new ArrayCollection();
        }
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions[] = $reaction;
            $reaction->setMessage($this);
        }
        return $this;
    }
}
