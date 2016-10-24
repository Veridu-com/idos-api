<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * Widgets RELATED TABLES.
 * This table holds the widget one-line of code customer's data.
 */
class Widgets extends AbstractMigration {
    public function change() {
        // Widgets
        $widgets = $this->table('widgets');
        $widgets
            ->addColumn('hash', 'text', ['null' => false])
            ->addColumn('label', 'text', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('enabled', 'boolean', ['default' => true])
            ->addColumn('config', 'jsonb', ['null' => false])
            ->addColumn('creator_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id', 'creator_id'])
            ->addIndex(['hash'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
