<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle("A first post!");
        $blogPost->setPublished(new \DateTime('2018-06-01 12:30:00'));
        $blogPost->setContent("Post text!");
        $blogPost->setAuthor("Resat Nuri");
        $blogPost->setSlug("a-fist-post");

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle("A seconds post!");
        $blogPost->setPublished(new \DateTime('2018-05-14 16:00:00'));
        $blogPost->setContent("Post text!");
        $blogPost->setAuthor("Kemal Sunal");
        $blogPost->setSlug("a-second-post");

        $manager->persist($blogPost);

        $manager->flush();
    }
}
