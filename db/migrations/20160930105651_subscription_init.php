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
            ->addColumn('category_slug', 'text', ['null' => true])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['category_slug', 'credential_id', 'identity_id'])
            ->addIndex(['category_slug', 'credential_id'], ['unique' => true])
            ->addForeignKey('category_slug', 'categories', 'slug', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
