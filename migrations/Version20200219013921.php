<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200219013921 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add UT Martin Digital Archive URL.';
    }

    public function up(Schema $schema) : void
    {
        $issueTable = $schema->getTable('issue');
        $issueTable->addColumn('utm_digital_archive_url', 'string', ['length' => 255, 'notnull' => false]);
        $issueTable->addUniqueIndex(['utm_digital_archive_url'], 'UNIQ_12AD233E58E11750');
    }

    public function down(Schema $schema) : void
    {
        $issueTable = $schema->getTable('issue');
        $issueTable->dropIndex('UNIQ_12AD233E58E11750');
        $issueTable->dropColumn('utm_digital_archive_url', 'string', ['length' => 255, 'notnull' => false]);
    }
}
