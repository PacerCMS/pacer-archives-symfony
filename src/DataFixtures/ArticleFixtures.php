<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\Entity\Article;
use App\Entity\Issue;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $issue = $manager->getRepository(Issue::class)->findOneBy(['issueDate' => new \DateTime('1928-12-17')]);

        $article = new Article();
        $article->setHeadline('Test Article 1');
        $article->setAlternativeHeadline('An article to test with');
        $article->setAuthorByline('Clark Kent, City Reporter');
        $article->setContributorByline('');
        $article->setKeywords('test,keywords');
        $article->setModifiedBy('user@example.com');
        $article->setArticleBody("Some text.\n##Sub headline\n[link](https://www.google.com)");
        $article->setPrintPage('1');
        $article->setPrintColumn('2');
        $article->setPrintSection('Cover');
        $article->setLegacyId('100');
        $manager->persist($article);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            IssueFixtures::class,
        );
    }
}
