<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * USER RELATED TABLES.
 */
class UserInit extends AbstractMigration {
    public function change() {
        // Links a user to an identity
        $links = $this->table('links');
        $links
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex('identity_id')
            ->addIndex('user_id')
            ->addIndex(['identity_id', 'user_id'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Profile attributes values
        $attributes = $this->table('attributes');
        $attributes
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Profile references values
        $references = $this->table('references');
        $references
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // User sources
        $sources = $this->table('sources');
        $sources
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('tags', 'jsonb', ['null' => true])
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // FIXME Review this table
        $userAccess = $this->table('user_access');
        $userAccess
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('resource', 'text', ['null' => true])
            ->addColumn('access', 'boolean', ['null' => false, 'default' => true])
            ->addTimestamps()
            ->addIndex('identity_id')
            ->addIndex('user_id')
            ->addIndex('resource')
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Company members (FIXME Review this table)
        $members = $this->table('members');
        $members
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => false, 'default' => 'member'])
            ->addTimestamps()
            ->addIndex('company_id')
            ->addIndex('user_id')
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $warnings = $this->table('warnings');
        $warnings
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('reference', 'text')
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

        $processes = $this->table('processes');
        $processes
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('event', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $tasks = $this->table('tasks');
        $tasks
            ->addColumn('process_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('event', 'text', ['null' => false])
            ->addColumn('running', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('success', 'boolean', ['null' => true])
            ->addColumn('message', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('process_id')
            ->addForeignKey('process_id', 'processes', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
