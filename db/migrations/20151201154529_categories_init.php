<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * DATA SUPPORT TABLES.
 */
class CategoriesInit extends AbstractMigration {
    public function change() {

        $categories = $this->table('categories');
        $categories
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('type', 'text', ['null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('service_id', 'integer', ['null' => false])
            ->addIndex('type')
            ->addIndex('slug', ['unique' => true])
            ->addTimestamps()
            ->addForeignKey('service_id', 'services', 'id', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->create();
    }
}
