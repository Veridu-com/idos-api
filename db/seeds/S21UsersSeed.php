<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S21UsersSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'credential_id' => 1,
                'username'      => md5('JohnDoe1'), // f67b96dcf96b49d713a520ce9f54053c
                'role'          => 'company.reviewer',
                'created_at'    => date('Y-m-d H:i:s', strtotime('now - 3 days')),
            ],
            [
                'credential_id' => 1,
                'username'      => md5('JohnDoe2'), // fd1fde2f31535a266ea7f70fdf224079
                'created_at'    => date('Y-m-d H:i:s', strtotime('now + 2 days')),
            ],
            [
                'credential_id' => 1,
                'username'      => md5('JohnDoeAdmin'), // 9faf3b682ab323456268b464c9569c0b
                'role'          => 'company.admin',
                'created_at'    => date('Y-m-d H:i:s', strtotime('now - 2 days')),
            ],
            [
                'credential_id' => 1,
                'username'      => md5('JohnDoeMember'), // 9b1f26a30ca446987eebf3a0509a5dee
                'role'          => 'company.owner'
            ],
            [
                'credential_id' => 1,
                'username'      => md5('JohnDoeGuest'), // edca494049b9a7c6a650b13118d5563d
                'role'          => 'guest'
            ]
        ];

        $table = $this->table('users');
        $table
            ->insert($data)
            ->save();
    }
}
