<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * SPECIAL TABLES.
 */
class SpecialInit extends AbstractMigration {
    public function change() {
        $addressLookup = $this->table('address_lookup');
        $addressLookup
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('reference', 'text', ['null' => true])
            ->addColumn('region', 'text', ['null' => false])
            ->addColumn('postcode', 'text', ['null' => false])
            ->addColumn('number', 'integer', ['null' => false])
            ->addColumn('street', 'text', ['null' => true])
            ->addColumn('city', 'text', ['null' => true])
            ->addColumn('state', 'text', ['null' => true])
            ->addColumn('country', 'text', ['null' => true])
            ->addTimestamps()
            ->addIndex('reference')
            ->addIndex('postcode')
            ->create();

        // This table has no Foreign Keys as it's used as logs for deleted companies/credentials/users too
        $logs = $this->table('logs');
        $logs
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('level', 'text', ['null' => false])
            ->addColumn('message', 'text', ['null' => false])
            ->addColumn('context', 'binary', ['null' => true])
            ->addTimestamps()
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addIndex('company_id')
            ->addIndex('credential_id')
            ->addIndex('user_id')
            ->create();
    }
}
