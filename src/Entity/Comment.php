<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(security:"is_granted('ROLE_COMMENTATOR')"),
        new Put(security:"is_granted('ROLE_EDITOR') || (is_granted('ROLE_COMMENTATOR') && object.getAuthor()==user)"),
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/blog_posts/{id}/comments',
            uriVariables: [
                'id' => new Link(fromProperty: 'comments', fromClass: BlogPost::class)
            ],
            normalizationContext: ['groups' => ['get-comment-with-author']]
        )
    ],
    denormalizationContext: ['groups' => ['post']],
    order: ['published'=>'ASC']
)]
class Comment implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get-comment-with-author'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 3000)]
    #[Groups(['post', 'get-comment-with-author'])]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['get-comment-with-author'])]
    private ?\DateTimeInterface $published = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User",  inversedBy: "comments")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get-comment-with-author'])]
    private ?User $author;

    #[ORM\ManyToOne(targetEntity: "App\Entity\BlogPost", inversedBy: "comments")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post'])]
    private ?BlogPost $blogPost;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return BlogPost
     */
    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    /**
     * @param BlogPost $blogPost
     */
    public function setBlogPost(BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;
        return $this;
    }

}
