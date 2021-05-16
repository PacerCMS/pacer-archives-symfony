<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\Entity\Article;
use App\Entity\Issue;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    const ARTICLE_1 = 'Article 1';

    public function load(ObjectManager $manager)
    {
        $issue = $this->getReference(IssueFixtures::ISSUE_1);

        $article = new Article();
        $article->setId(1);
        $article->setHeadline('Test Article 1');
        $article->setSlug('test-article-1');
        $article->setAlternativeHeadline('An article to test with');
        $article->setAuthorByline('Clark Kent, City Reporter');
        $article->setContributorByline('');
        $article->setKeywords('test,keywords');
        $article->setModifiedBy('user@example.com');
        $article->setArticleBody("Some text.\n##Sub headline\n[link](https://www.google.com)");
        $article->setDatePublished(new \DateTime('1928-12-17'));
        $article->setPrintPage('1');
        $article->setPrintColumn('2');
        $article->setPrintSection('Cover');
        $article->setLegacyId(100);
        $article->setIssue($issue);
        $manager->persist($article);

        // Enforce specified record ID
        $metadata = $manager->getClassMetaData(get_class($article));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();

        $this->addReference(self::ARTICLE_1, $article);
    }

    public function getDependencies()
    {
        return array(
            IssueFixtures::class,
        );
    }
}
