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
            ->addColumn('identity_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('username', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id', 'username'], ['unique' => true])
            ->addIndex('credential_id')
            ->addIndex('identity_id')
            ->addIndex('username')
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'SET NULL', 'update' => 'SET NULL'])
            ->create();

        $warnings = $this->table('warnings');
        $warnings
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('name')
            ->addIndex(['user_id', 'name'], ['unique' => true])
            ->addIndex(['user_id', 'slug'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $tags = $this->table('tags');
        $tags
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['user_id', 'slug'], ['unique' => true])
            ->addIndex('user_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $gates = $this->table('gates');
        $gates
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('pass', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('name')
            ->addIndex(['user_id', 'name'], ['unique' => true])
            ->addIndex(['user_id', 'slug'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
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
