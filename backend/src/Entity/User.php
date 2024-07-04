<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\NflTeam;
use App\Entity\Message;
use App\Entity\UserAchievement;
use App\Entity\Bet;
use App\Entity\ChatroomMessage;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: "App\Repository\UserRepository")]

class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $username;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\ManyToOne(targetEntity: NflTeam::class)]
    #[ORM\JoinColumn(name: "favTeam", referencedColumnName: "id", nullable: false)]
    private $favTeam;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: "sender")]
    private $sentMessages;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: "receiver")]
    private $receivedMessages;

    #[ORM\OneToMany(targetEntity: Bet::class, mappedBy: "user")]
    private $bets;

    #[ORM\OneToMany(targetEntity: UserAchievement::class, mappedBy: "user")]
    private $userAchievements;

    #[ORM\OneToMany(targetEntity: ChatroomMessage::class, mappedBy: "sender")]
    private $chatroomMessages;

    #[ORM\Column(type: 'string', nullable: true)]
    private $profilePicture;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'string')]
    private $bio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getFavTeam(): ?NflTeam
    {
        return $this->favTeam;
    }

    public function setFavTeam(?NflTeam $favTeam): self
    {
        $this->favTeam = $favTeam;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }



    public function __construct()
    {
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->bets = new ArrayCollection();
        $this->userAchievements = new ArrayCollection();
        $this->chatroomMessages = new ArrayCollection();
    }

    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function getBets(): Collection
    {
        return $this->bets;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUserAchievements(): Collection
    {
        return $this->userAchievements;
    }

    public function getChatroomMessages(): Collection
    {
        return $this->chatroomMessages;
    }
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        // This method should return a string that uniquely identifies this user.
        // For example, it could return the username, email, or a UUID.
        return $this->username;
    }
}
