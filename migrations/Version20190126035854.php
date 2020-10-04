<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190126035854 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Baseline schema.';
    }

    public function up(Schema $schema) : void
    {
        $articleTable = $schema->createTable('article');
        $articleTable->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $articleTable->addColumn('issue_id', 'integer', ['notnull' => false]);
        $articleTable->addColumn('print_column', 'string', ['length' => 255]);
        $articleTable->addColumn('print_page', 'string', ['length' => 255]);
        $articleTable->addColumn('print_section', 'string', ['length' => 255]);
        $articleTable->addColumn('article_body', 'text', ['notnull' => true]);
        $articleTable->addColumn('headline', 'string', ['length' => 255, 'notnull' => true]);
        $articleTable->addColumn('alternative_headline', 'string', ['length' => 255]);
        $articleTable->addColumn('author_byline', 'string', ['length' => 255, 'notnull' => true]);
        $articleTable->addColumn('contributor_byline', 'string', ['length' => 255]);
        $articleTable->addColumn('date_created', 'datetime', ['notnull' => true]);
        $articleTable->addColumn('date_published', 'datetime', ['notnull' => true]);
        $articleTable->addColumn('date_modified', 'datetime', ['notnull' => true]);
        $articleTable->addColumn('keywords', 'text', ['notnull' => true]);
        $articleTable->addColumn('modified_by', 'string', ['length' => 255, 'notnull' => true]);
        $articleTable->addColumn('legacy_id', 'integer');
        $articleTable->addColumn('slug', 'string', ['length' => 128, 'notnull' => true]);
        $articleTable->addIndex(['issue_id'], 'IDX_23A0E665E7AA58C');
        $articleTable->addUniqueIndex(['slug'], 'UNIQ_23A0E66989D9B62');
        $articleTable->setPrimaryKey(['id']);

        $volumeTable = $schema->createTable('volume');
        $volumeTable->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $volumeTable->addColumn('cover_issue_id', 'integer', ['notnull' => false]);
        $volumeTable->addColumn('volume_number', 'string', ['length' => 4]);
        $volumeTable->addColumn('volume_start_date', 'date', ['notnull' => true]);
        $volumeTable->addColumn('volume_end_date', 'date', ['notnull' => true]);
        $volumeTable->addColumn('nameplate_key', 'string', ['length' => 10, 'notnull' => true]);
        $volumeTable->addUniqueIndex(['cover_issue_id'], 'UNIQ_B99ACDDE867F63D7');
        $volumeTable->setPrimaryKey(['id']);

        $issueTable = $schema->createTable('issue');
        $issueTable->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $issueTable->addColumn('volume_id', 'integer');
        $issueTable->addColumn('issue_date', 'date', ['notnull' => true]);
        $issueTable->addColumn('issue_number', 'string', ['length' => 255, 'notnull' => true]);
        $issueTable->addColumn('page_count', 'text', ['notnull' => true]);
        $issueTable->addColumn('archive_key', 'string', ['length' => 255]);
        $issueTable->addColumn('archive_notes', 'text');
        $issueTable->addUniqueIndex(['archive_key'], 'UNIQ_12AD233E1AB9D946');
        $issueTable->addIndex(['volume_id'], 'IDX_12AD233E8FD80EEA');
        $issueTable->setPrimaryKey(['id']);

        $userTable = $schema->createTable('user');
        $userTable->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $userTable->addColumn('email', 'string', ['length' => 180, 'notnull' => true]);
        $userTable->addColumn('roles', 'text', ['notnull' => true, 'comment' => '(DC2Type:json)']);
        $userTable->addColumn('password', 'string', ['length' => 255, 'notnull' => true]);
        $userTable->addUniqueIndex(['email'], 'UNIQ_8D93D649E7927C74');
        $userTable->setPrimaryKey(['id']);

        $imageTable = $schema->createTable('image');
        $imageTable->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $imageTable->addColumn('article_id', 'integer', ['notnull' => true]);
        $imageTable->addColumn('caption', 'text', ['notnull' => true]);
        $imageTable->addColumn('credit', 'string', ['length' => 255, 'notnull' => true]);
        $imageTable->addColumn('path', 'string', ['length' => 255, 'notnull' => true]);
        $imageTable->addIndex(['article_id'], 'IDX_C53D045F7294869C');
        $imageTable->setPrimaryKey(['id']);

        $articleTable->addForeignKeyConstraint($issueTable, ['issue_id'], ['id'], [], 'FK_23A0E665E7AA58C');
        $volumeTable->addForeignKeyConstraint($issueTable, ['cover_issue_id'], ['id'], [], 'FK_B99ACDDE867F63D7');
        $issueTable->addForeignKeyConstraint($volumeTable, ['volume_id'], ['id'], [], 'FK_12AD233E8FD80EEA');
        $imageTable->addForeignKeyConstraint($articleTable, ['article_id'], ['id'], [], 'FK_C53D045F7294869C');
    }

    public function down(Schema $schema) : void
    {
        $schema->getTable('image')->removeForeignKey('FK_C53D045F7294869C');
        $schema->getTable('issue')->removeForeignKey('FK_12AD233E8FD80EEA');
        $schema->getTable('article')->removeForeignKey('FK_23A0E665E7AA58C');
        $schema->getTable('volume')->removeForeignKey('FK_B99ACDDE867F63D7');
        $schema->dropTable('article');
        $schema->dropTable('volume');
        $schema->dropTable('issue');
        $schema->dropTable('user');
        $schema->dropTable('legacy_article');
        $schema->dropTable('image');
    }
}
