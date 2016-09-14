<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * ATTRIBUTE RELATED TABLES.
 */
class AttributeInit extends AbstractMigration {
    public function change() {
        // Attribute scores
        $scores = $this->table('scores');
        $scores
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'float', ['null' => false, 'default' => 0.0])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('creator')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'services', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
