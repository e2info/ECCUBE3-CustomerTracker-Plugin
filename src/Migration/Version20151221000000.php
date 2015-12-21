<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151221000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->createHistoryTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_ct_history');
    }

    protected function createHistoryTable(Schema $schema)
    {
        $table = $schema->createTable("plg_ct_history");
        $table->addColumn('history_id', 'integer', array(
            'notnull' => true,
            'autoincrement' => true
        ));
        $table->addColumn('customer_id', 'integer', array(
            'notnull' => false
        ));
        $table->addColumn('session_id', 'string', array(
            'length' => 255,
            'notnull' => true
        ));
        $table->addColumn('uri', 'string', array(
            'length' => 255,
            'notnull' => true
        ));
        $table->addColumn('referrer', 'string', array(
            'length' => 255,
            'notnull' => false
        ));
        $table->addColumn('visited', 'datetime', array(
            'notnull' => true
        ));
        $table->addColumn('stayed', 'integer', array(
            'notnull' => false
        ));
        $table->addIndex(array(
            'customer_id',
            'session_id',
            'visited'
        ), 'customer_session_visited_key');
        $table->setPrimaryKey(array(
            'history_id'
        ));
    }
}