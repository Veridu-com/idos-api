<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S26UserIdentitiesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'identity_id' => 1,
                'user_id'     => 1
            ],
            [
                'identity_id' => 2,
                'user_id'     => 2
            ],
            [
                'identity_id' => 3,
                'user_id'     => 2
            ]
        ];

        $table = $this->table('user_identities');
        $table
            ->insert($data)
            ->save();
    }
}
