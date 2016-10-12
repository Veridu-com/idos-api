<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * IDENTITY RELATED TABLES.
 */
class IdentityInit extends AbstractMigration {
    public function change() {
        // Roles
        $features = $this->table('roles');
        $features
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('rank', 'integer', ['null' => false])
            ->addColumn('bit', 'integer', ['null' => false])
            ->addColumn('created_at', 'timestamp', ['null' => false, 'timezone' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('name', ['unique' => true])
            ->create();

        // Roles for rights management
        $roleAccess = $this->table('role_access');
        $roleAccess
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => true])
            ->addColumn('resource', 'text', ['null' => true])
            ->addColumn('access', 'integer', ['null' => false, 'default' => 0x00]) // values [ none=0x00, exec=0x01, w=0x02, r=0x04, r-exec=0x05, rw=0x06, rw-exec=0x07 ]
            ->addTimestamps()
            ->addIndex('identity_id')
            ->addIndex('role')
            ->addIndex('resource')
            ->addIndex(['identity_id', 'role', 'resource'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'SET NULL', 'update' => 'SET NULL'])
            ->create();

        // Company members (FIXME Review this table)
        $members = $this->table('members');
        $members
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => false, 'default' => 'member'])
            ->addTimestamps()
            ->addIndex(['company_id', 'identity_id'], ['unique' => true])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
