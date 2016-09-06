<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * WARNING RELATED TABLES.
 */
class WarningsInit extends AbstractMigration {
    public function change() {
        // Profile reviews values
        $reviews = $this->table('reviews');
        $reviews
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('warning_id', 'integer', ['null' => false])
            ->addColumn('positive', 'boolean', ['null' => false])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('warning_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('warning_id', 'warnings', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
