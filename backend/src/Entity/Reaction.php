<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\ChatroomMessage;

#[ORM\Entity(repositoryClass: 'App\Repository\ReactionRepository')]
class Reaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $reactionCode;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "userID", referencedColumnName: "id")]
    private $user;

    #[ORM\ManyToOne(targetEntity: ChatroomMessage::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(name: "messageID", referencedColumnName: "id")]
    private $message;

    function getId(): ?int
    {
        return $this->id;
    }

    function getReactionCode(): ?string
    {
        return $this->reactionCode;
    }

    function setReactionCode(string $reactionCode): self
    {
        $this->reactionCode = $reactionCode;
        return $this;
    }

    function getUser(): ?User
    {
        return $this->user;
    }

    function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    function getMessage(): ?ChatroomMessage
    {
        return $this->message;
    }

    function setMessage(?ChatroomMessage $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'reactionCode' => $this->getReactionCode(),
            // Include any other properties you want in the response
        ];
    }
}
