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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity("username")]
#[UniqueEntity("email")]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['get']], security: "is_granted('ROLE_USER')"),
        new Post(normalizationContext: ['groups' => ['get']], denormalizationContext: ['groups' => ['post']]),
        new Put(normalizationContext: ['groups' => ['get']], denormalizationContext: ['groups' => ['put']], security: "is_granted('ROLE_USER') and object==user"),
        new GetCollection()
    ]
)]

class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'post'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 6, max: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['put', 'post'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
        message: "Password must be seven character long and contain at least one digit, one uppercase"
    )]
    private ?string $password = null;

    #[Assert\NotBlank()]
    #[Groups(['put', 'post'])]
    #[Assert\Expression(
        "this.getPassword() === this.getRetypedPassword()",
        message: "Password doesnt match"
    )]
    private ?string $retypedPassword = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'put', 'post'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post', 'put'])]
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

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
        // TODO: Implement getRoles() method.
        return ['ROLE_USER'];
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
}
