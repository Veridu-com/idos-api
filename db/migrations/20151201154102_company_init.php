<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * COMPANY RELATED TABLES.
 */
class CompanyInit extends AbstractMigration {
    public function change() {
        // Company Settings
        $settings = $this->table('settings');
        $settings
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('section', 'text', ['null' => false])
            ->addColumn('property', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => false])
            ->addColumn('protected', 'boolean', ['null' => false, 'default' => false])
            ->addTimestamps()
            ->addIndex('company_id')
            ->addIndex(['company_id', 'section', 'property'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Company Endpoint Permission
        $permissions = $this->table('permissions');
        $permissions
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('route_name', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex('company_id')
            ->addIndex(['company_id', 'route_name'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Company credentials for API access
        $credentials = $this->table('credentials');
        $credentials
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('public', 'text', ['null' => false])
            ->addColumn('private', 'text', ['null' => false])
            ->addColumn('production', 'boolean', ['null' => false, 'default' => false])
            ->addTimestamps()
            ->addIndex('company_id')
            ->addIndex('slug')
            ->addIndex('public', ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
