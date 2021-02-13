<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\Entity\Article;
use App\Entity\Image;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $article = $manager->getRepository(Article::class)->findOneBy(['headline' => 'Test Article 1']);

        $image = new Image();
        $image->setArticle($article);
        $image->setCaption('A photo caption.');
        $image->setCredit('Peter Parker');
        $image->setPath('photo_upload/1928-12-17/photo.jpg');
        $manager->persist($image);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ArticleFixtures::class,
        );
    }
}
