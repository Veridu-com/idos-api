<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * CREDENTIAL RELATED TABLES.
 */
class CredentialInit extends AbstractMigration {
    public function change() {
        // Identity link to credentials
        $users = $this->table('users');
        $users
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => true])
            ->addColumn('username', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id', 'username'], ['unique' => true])
            ->addIndex('credential_id')
            ->addIndex('username')
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'SET NULL', 'update' => 'SET NULL'])
            ->create();

        // Credential WebHooks
        $hooks = $this->table('hooks');
        $hooks
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('trigger', 'text', ['null' => false])
            ->addColumn('url', 'binary', ['null' => false])
            ->addColumn('subscribed', 'boolean', ['null' => false, 'default' => 'TRUE'])
            ->addTimestamps()
            ->addIndex('credential_id')
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
