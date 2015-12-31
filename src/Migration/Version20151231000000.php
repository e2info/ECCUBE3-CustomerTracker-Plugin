<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151231000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        if ($this->connection->getDatabasePlatform()->getName() == 'mysql') {
            $this->addSql('ALTER TABLE plg_ct_history ADD prev_uri VARCHAR(255) NULL AFTER uri');
        }
    }

    public function down(Schema $schema)
    {
    }
}