<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Controller\UploadImageAction;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity]
#[Vich\Uploadable]
//#[ApiResource(routePrefix: '/images')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            uriTemplate: "/images",
            defaults: ["_api_receive"=> false],
            controller: UploadImageAction::class,
        )
    ],
    order: ['id'=>'DESC']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ["id"=>"exact" , "title"=>"partial"]
)]

class Image
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: "images", fileNameProperty: "url")]
    //#[Assert\NotNull]
    private ?File $file = null;

    #[ORM\Column( nullable: true)]
    #[Groups(['get-blog-post-with-author'])]
    private ?string $url = null;

    public function getId()
    {
        return $this->id;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): void
    {
        $this->file = $file;
    }

    public function getUrl()
    {
        return '/images/' . $this->url;
    }

    public function setUrl($url): void
    {
        $this->url = $url;
    }

    public function __toString()
    {
        return $this->id . ':' . $this->url;
    }
}
