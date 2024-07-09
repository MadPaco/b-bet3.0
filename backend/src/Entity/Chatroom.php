<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\ChatroomMessage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: 'App\Repository\ChatroomRepository')]

class Chatroom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(targetEntity: ChatroomMessage::class, mappedBy: "chatroom", cascade: ['persist', 'remove'])]
    private $messages;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }
}
