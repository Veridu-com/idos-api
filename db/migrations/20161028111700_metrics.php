<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * Metrics RELATED TABLES.
 * These tables holds API metrics information.
 */
class Metrics extends AbstractMigration {
    public function change() {
        /**
         * Gate Related Metrics
         */
        $this
            ->table('gate_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('pass', 'boolean', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['name'])
            ->addIndex(['pass'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('gate_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('pass', 'boolean', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['name'])
            ->addIndex(['pass'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('gate_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('pass', 'boolean', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['name'])
            ->addIndex(['pass'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Source Related Metrics
         */
        $this
            ->table('source_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('sso', 'boolean', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['provider'])
            ->addIndex(['action'])
            ->addIndex(['sso'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('source_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('sso', 'boolean', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['provider'])
            ->addIndex(['action'])
            ->addIndex(['sso'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('source_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('sso', 'boolean', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['provider'])
            ->addIndex(['action'])
            ->addIndex(['sso'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
