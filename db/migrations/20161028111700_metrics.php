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
        //select * from categories;

        $this
            ->table('metrics')
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addIndex(['credential_public'])
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_hourly')
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['credential_public'])
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_daily')
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['credential_public'])
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_user')
            ->addColumn('hash', 'text', ['null' => false])
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('sources', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('gates', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('flags', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['hash'], ['unique' => true])
            ->addIndex(['credential_public'])
            ->create();
    }
}
