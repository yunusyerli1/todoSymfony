<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity("username")]
#[UniqueEntity("email")]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['get']], security: "is_granted('ROLE_COMMENTATOR')"),
        new Post(normalizationContext: ['groups' => ['get']], denormalizationContext: ['groups' => ['post']]),
        new Put(normalizationContext: ['groups' => ['get']], denormalizationContext: ['groups' => ['put']], security: "is_granted('ROLE_COMMENTATOR') && object==user"),
        new GetCollection()
    ]
)]

class User implements PasswordAuthenticatedUserInterface, UserInterface
{

    const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'post', 'get-comment-with-author', 'get-blogpost-author'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 6, max: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
        message: "Password must be seven character long and contain at least one digit, one uppercase"
    )]
    private ?string $password = null;

    #[Assert\NotBlank()]
    #[Groups(['post'])]
    #[Assert\Expression(
        "this.getPassword() === this.getRetypedPassword()",
        message: "Password doesnt match"
    )]
    private ?string $retypedPassword = null;

    //#[ORM\Column(length: 255)]
    #[Groups(['put-reset-password'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
        message: "Password must be seven character long and contain at least one digit, one uppercase"
    )]
    private ?string $newPassword = null;

    #[Groups(['put-reset-password'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        "this.getNewPassword() === this.getNewRetypedPassword()",
        message: "Password must be seven character long and contain at least one digit, one uppercase"
    )]
    private ?string $newRetypedPassword = null;

    #[Groups(['put-reset-password'])]
    #[Assert\NotBlank()]
    #[UserPassword()]
    private ?string $oldPassword = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'put', 'post', 'get-comment-with-author', 'get-blogpost-author'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post', 'put', 'get-admin', 'get-owner'])]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[Assert\Length(min: 6, max: 255)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: "author", targetEntity: "App\Entity\BlogPost")]
    #[Groups(['get'])]
    private $posts;

    #[ORM\OneToMany(mappedBy: "author", targetEntity: "App\Entity\Comment")]
    #[Groups(['get'])]
    private $comments;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, length: 200)]
    #[Groups(['get-admin', 'get-owner'])]
    private $roles;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
    }

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRetypedPassword(): ?string
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword(?string $retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
        return $this->username;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    public function setNewRetypedPassword(?string $newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(?string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

}
