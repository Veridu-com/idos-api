<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * HOOK RELATED TABLES.
 */
class HookInit extends AbstractMigration {
    public function change() {
        // Hook errors
        $hookErrors = $this->table('hook_errors');
        $hookErrors
            ->addColumn('hook_id', 'integer', ['null' => false])
            ->addColumn('payload', 'binary', ['null' => false])
            ->addColumn('error', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('hook_id')
            ->addForeignKey('hook_id', 'hooks', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
