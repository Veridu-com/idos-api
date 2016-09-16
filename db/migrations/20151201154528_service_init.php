<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * SERVICE RELATED TABLES.
 */
class ServiceInit extends AbstractMigration {
    public function change() {
        // Service list
        $services = $this->table('services');
        $services
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('url', 'text', ['null' => false])
            ->addColumn('auth_username', 'text', ['null' => false])
            ->addColumn('auth_password', 'text', ['null' => false])
            ->addColumn('public', 'text', ['null' => false])
            ->addColumn('private', 'text', ['null' => false])
            ->addColumn('listens', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('triggers', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('enabled', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('access', 'integer', ['null' => false, 'default' => 0x01]) // 0x00 => 'private', 0x01 => 'company' (visible by children), 0x02 => 'public'
            ->addTimestamps()
            ->addIndex('name', ['unique' => true])
            ->addIndex('public', ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Service handlers registration
        $serviceHandlers = $this->table('service_handlers');
        $serviceHandlers
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('service_id', 'integer', ['null' => false])
            ->addColumn('listens', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['company_id', 'service_id']) // Must be in array "events" of the the related "service"
            ->addIndex(['service_id', 'company_id'], ['unique' => true])
            ->addForeignKey('service_id', 'services', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
