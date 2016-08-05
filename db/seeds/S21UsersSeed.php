<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S21UsersSeed extends AbstractSeed {
    public function run() {
        $usersData = [
            [
                'credential_id' => 1,
                'identity_id'   => 1,
                'username'      => md5('JohnDoe')       // 9fd9f63e0d6487537569075da85a0c7f2
            ],
            [
                'credential_id' => 1,
                'identity_id'   => 2,
                'username'      => md5('JohnDoe') . '2' // 9fd9f63e0d6487537569075da85a0c7f2
            ]
        ];

        $table = $this->table('users');
        $table
            ->insert($usersData)
            ->save();
    }
}
