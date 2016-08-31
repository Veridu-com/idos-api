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
            ->addColumn('attribute_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'float', ['null' => false, 'default' => 0.0])
            ->addTimestamps()
            ->addIndex('attribute_id')
            ->addForeignKey('attribute_id', 'attributes', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
