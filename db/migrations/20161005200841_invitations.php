<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * Invitations RELATED TABLES.
 * This table holds the invitations to the dashboard.
 */
class Invitations extends AbstractMigration {
    public function change() {
        // Invitations
        $invitations = $this->table('invitations');
        $invitations
            ->addColumn('email', 'text', ['null' => false])
            ->addColumn('role', 'text', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('member_id', 'integer', ['null' => true])
            ->addColumn('creator_id', 'integer', ['null' => false])
            ->addColumn('expires', 'timestamp', ['null' => false])
            ->addColumn('hash', 'text', ['null' => false])
            ->addColumn('voided', 'boolean', ['null' => false, 'default' => false])
            ->addTimestamps()
            ->addIndex(['credential_id', 'creator_id'])
            ->addIndex(['company_id', 'member_id'], ['unique' => true])
            ->addIndex(['hash'], ['unique' => true])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('member_id', 'members', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
