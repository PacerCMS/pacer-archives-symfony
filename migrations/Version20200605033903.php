<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200605033903 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add search index for articles.';
    }

    public function up(Schema $schema) : void
    {
        $articleTable = $schema->getTable('article');

        $articleTable->addIndex(['article_body', 'headline', 'alternative_headline'], 'IDX_23A0E666BEA8CB3E0E861BDABEFA08E');
        $index = $articleTable->getIndex('IDX_23A0E666BEA8CB3E0E861BDABEFA08E');
        $index->addFlag('fulltext');

        $articleTable->addIndex(['author_byline', 'contributor_byline'], 'IDX_23A0E6615AFD08B8597F21E');
        $index = $articleTable->getIndex('IDX_23A0E6615AFD08B8597F21E');
        $index->addFlag('fulltext');
    }

    public function down(Schema $schema) : void
    {
        $articleTable = $schema->getTable('article');
        $articleTable->dropIndex('IDX_23A0E666BEA8CB3E0E861BDABEFA08E');
        $articleTable->dropIndex('IDX_23A0E6615AFD08B8597F21E');
    }
}
