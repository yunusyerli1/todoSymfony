<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiSubresource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['published' => 'exact', 'title' => 'partial', 'content' => 'partial', 'author' => 'exact', 'author.name' => 'exact']
)]
#[ApiFilter(
    DateFilter::class,
    properties: ['published']
)]
#[ApiFilter(
    RangeFilter::class,
    properties: ['id']
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['id', 'published', 'title']
)]

#[ApiFilter(
    PropertyFilter::class,
    arguments: ['parameterName' => 'properties', 'overrideDefaultProperties'=> false, 'whitelist'=>['id', 'author', 'slug']]
)]
#[ApiResource(
    operations: [
    new Get(normalizationContext: ['groups' => ['get-blogpost-author']]),
    new Post(security:"is_granted('ROLE_EDITOR') || is_granted('ROLE_WRITER')"),
    new Put(security:"is_granted('ROLE_EDITOR') ||  (is_granted('ROLE_WRITER') && object.getAuthor()==user)"),
    new GetCollection()
    ],
    denormalizationContext: ['groups' => ['post']]
)]
class BlogPost implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get-blogpost-author'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 10)]
    #[Groups(['post','get-blogpost-author'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['get-blogpost-author'])]
    private ?\DateTimeInterface $published = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 20)]
    #[Groups(['post','get-blogpost-author'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User",  inversedBy: "posts")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get-blogpost-author'])]
    private User $author;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank()]
    #[Groups(['post','get-blogpost-author'])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: "blogPost", targetEntity: "App\Entity\Comment")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get-blogpost-author'])]
    private $comments;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Image")]
    #[ORM\JoinTable()]
    #[Groups(['post', 'get-blog-post-with-author'])]
    private  $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImages(Image $image)
    {
        return $this->add($image);
    }

    public function removeImage(Image $image)
    {
        return $this->removeElement($image);
    }

    public function __toString()
    {
        return substr($this->content, 0,20) . '...';
    }

}
