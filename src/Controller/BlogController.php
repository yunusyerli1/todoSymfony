<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/blog')]
class BlogController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly SerializerInterface $serializer) {}

    #[Route('/', name: 'blog_list', requirements: ["page"=> "\d+"], defaults: ["page"=> 5])]
    public function list($page)
    {
        $repository = $this->entityManager->getRepository(BlogPost::class);
        $items = $repository->findAll();
        return $this->json([
            'page' => $page,
            'data'=> $items
        ]);
    }

//    /**
//     * @Route("/{page}", name="blog_routes", defaults={"page":5}, requirements={"page"="\d+"})
//     */
    #[Route('/routes', name: 'blog_routes', requirements: ["page"=> "\d+"], defaults: ["page"=> 5])]
    public function listRoute($page=2, Request $request)
    {
        $limit= $request->get('limit', 10);
        $repository = $this->entityManager->getRepository(BlogPost::class);
        $items = $repository->findAll();
        return $this->json([
            'page' => $page,
            'limit'=> $limit,
            'data'=> array_map(function (BlogPost $item){
                return $this->generateUrl('blog_by_slug', ['slug'=> $item->getSlug()]);
            },$items)
        ]);
    }

    #[Route('/post/{id}', name: 'blog_by_id', requirements: ["page"=> "\d+"], methods: 'GET')]
    #[Entity("post", class: "App\Entity\BlogPost")]
    public function post($post)
    {
        return $this->json(
            $post
            //$this->entityManager->getRepository(BlogPost::class)->find($id)
        );
    }

    #[Route('/post/{slug}', name: 'blog_by_slug', methods: 'GET')]
    #[Entity("post", class: "App\Entity\BlogPost",options: ["mapping"=>["slug"=>"slug"]])]
    public function postBySlug($post)
    {
        //Same as doing findOneBy(['slug' => $slug])
        return $this->json($post);
    }

//    public function postBySlug($slug)
//    {
//        return $this->json(
//            $this->entityManager->getRepository(BlogPost::class)->findOneBy(array('slug' => $slug))
//        );
//    }

    #[Route('/add', name: 'blog_add', methods: ['POST'])]
    public function add(Request $request)
    {
        $blogPost = $this->serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $this->entityManager ->persist($blogPost);
        $this->entityManager->flush();

        return $this->json($blogPost);
    }

    #[Route('/delete/{id}', name: 'blog_delete', methods: ['POST'])]
    public function delete(BlogPost $post)
    {
        $this->entityManager ->remove($post);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
