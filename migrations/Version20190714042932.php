<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190714042932 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds TimelineEvent entity';
    }

    public function up(Schema $schema) : void
    {
        $timelineEventTable = $schema->createTable('timeline_event');
        $timelineEventTable->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $timelineEventTable->addColumn('start_date', 'datetime', ['notnull' => true]);
        $timelineEventTable->addColumn('start_date_precision', 'string', ['length' => 255, 'notnull' => true]);
        $timelineEventTable->addColumn('end_date', 'datetime', ['notnull' => false]);
        $timelineEventTable->addColumn('end_date_precision', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('display_date', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('headline', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('text', 'text', ['notnull' => false]);
        $timelineEventTable->addColumn('media', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('media_credit', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('media_caption', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('media_thumbnail', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('type', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('event_group', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->addColumn('background', 'string', ['length' => 255, 'notnull' => false]);
        $timelineEventTable->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('timeline_event');
    }
}
