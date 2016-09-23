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
        // Features
        $features = $this->table('features');
        $features
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('source', 'text', ['null' => true])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('value', 'binary')
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('source')
            ->addIndex('creator')
            ->addIndex(['user_id', 'source', 'creator', 'id'])
            ->addIndex(['user_id', 'source', 'creator', 'name'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'services', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
