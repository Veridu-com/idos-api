<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * ROOT TABLES.
 */
class DatabaseInit extends AbstractMigration {
    public function change() {
        // Root for a person's ID
        $identities = $this->table('identities');
        $identities
            ->addColumn('reference', 'text', ['null' => false])
            ->addColumn('public_key', 'text', ['null' => false])
            // private_key should be binary
            ->addColumn('private_key', 'binary', ['null' => false])
            ->addTimestamps()
            ->addIndex('public_key')
            ->create();

        // Company base info
        $companies = $this->table('companies');
        $companies
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('public_key', 'text', ['null' => false])
            // private_key should be binary
            ->addColumn('private_key', 'text', ['null' => false])
            ->addColumn('personal', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addTimestamps()
            ->addIndex('public_key')
            ->addIndex('slug', ['unique' => true])
            ->addForeignKey('parent_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
