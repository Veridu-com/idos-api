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
                'username'      => md5('JohnDoe1') // f67b96dcf96b49d713a520ce9f54053c
            ],
            [
                'credential_id' => 1,
                'identity_id'   => 2,
                'username'      => md5('JohnDoe2') // fd1fde2f31535a266ea7f70fdf224079
            ]
        ];

        $table = $this->table('users');
        $table
            ->insert($usersData)
            ->save();
    }
}
