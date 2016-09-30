<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * SUBSCRIPTION RELATED TABLES.
 */
class SubscriptionInit extends AbstractMigration {
    public function change() {
        // SUBSCRIPTIONS
        $subscriptions = $this->table('subscriptions');
        $subscriptions
            ->addColumn('gate_id', 'integer', ['null' => true])
            ->addColumn('warning_id', 'integer', ['null' => true])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['gate_id', 'warning_id', 'credential_id', 'identity_id'])
            ->addIndex(['gate_id', 'credential_id'], ['unique' => true])
            ->addIndex(['warning_id', 'credential_id'], ['unique' => true])
            ->addForeignKey('gate_id', 'gates', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('warning_id', 'warnings', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
