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
                'username'      => md5('JohnDoe1'),
                'role'          => 'company.member'
            ],
            [
                'credential_id' => 1,
                'identity_id'   => 2,
                'username'      => md5('JohnDoe2') // fd1fde2f31535a266ea7f70fdf224079
            ],
            [
                'credential_id' => 1,
                'identity_id'   => 1,
                'username'      => md5('JohnDoeAdmin'), // f67b96dcf96b49d713a520ce9f54053c
                'role'          => 'company.admin'
            ],
            [
                'credential_id' => 1,
                'identity_id'   => 1,
                'username'      => md5('JohnDoeMember'), // f67b96dcf96b49d713a520ce9f54053c
                'role'          => 'company.owner'
            ],
            [
                'credential_id' => 1,
                'identity_id'   => 1,
                'username'      => md5('JohnDoeGuest'), // f67b96dcf96b49d713a520ce9f54053c
                'role'          => 'guest'
            ]
        ];

        $table = $this->table('users');
        $table
            ->insert($usersData)
            ->save();
    }
}
