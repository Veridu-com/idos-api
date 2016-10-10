<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * FLAG RELATED TABLES.
 */
class FlagsInit extends AbstractMigration {
    public function change() {
        // Profile reviews values
        $reviews = $this->table('reviews');
        $reviews
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('flag_id', 'integer', ['null' => false])
            ->addColumn('positive', 'boolean', ['null' => false])
            ->addTimestamps()
            ->addIndex(['user_id', 'flag_id'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('flag_id', 'flags', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
