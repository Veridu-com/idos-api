<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * SOURCE RELATED TABLES.
 */
class SourceInit extends AbstractMigration {
    public function change() {
        // Normalised data
        $normalised = $this->table('normalised');
        $normalised
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('source_id')
            ->addIndex('name')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Digested data
        $digested = $this->table('digested');
        $digested
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('source_id')
            ->addIndex('name')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
