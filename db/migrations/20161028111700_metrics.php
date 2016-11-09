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
        //@FIXME make the id bigserial
        $this
            ->table('metrics')
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_hourly')
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_daily')
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();
    }
}
