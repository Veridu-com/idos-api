<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S40SubscriptionsSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'category_slug' => 'first-name-mismatch',
                'credential_id' => 1,
                'identity_id' => 1,
                'created_at' => $now
            ],
            [
                'category_slug' => 'last-name-mismatch',
                'credential_id' => 1,
                'identity_id'   => 1,
                'created_at'    => $now
            ]
        ];

        $subscriptions = $this->table('subscriptions');
        $subscriptions
            ->insert($data)
            ->save();
    }
}
