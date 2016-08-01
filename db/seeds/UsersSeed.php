<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class UsersSeed extends AbstractSeed {
    public function run() {
        $faker = Faker\Factory::create();

        $data = [];
        $now  = date('Y-m-d H:i:s');

        $identitiesData = [
            [
                'public_key'    => md5('hello'), // 5d41402abc4b2a76b9719d911017c592
                'private_key'   => md5('world')  // 7d793037a0760186574b0282f2f435e7
            ],
            [
                'public_key'    => md5('world'), // 7d793037a0760186574b0282f2f435e7
                'private_key'   => md5('hello')  // 5d41402abc4b2a76b9719d911017c592
            ]
        ];

        $usersData = [
            [
                'credential_id' => 1,
                'identity_id'   => 1,
                'username'      => md5('JohnDoe')   // 9fd9f63e0d6487537569075da85a0c7f  
            ],
            [
                'credential_id' => 2,
                'identity_id'   => 2,
                'username'      => md5('JohnDoe2')  // fd1fde2f31535a266ea7f70fdf224079
            ]
        ];

        $roleAccessData = [
            [
                'identity_id'   => 1,
                'role'          => 'user',
                'resource'      => 'roleAccess:listAll',
                'access'        => 0x04,
            ],
            [
                'identity_id'   => 2,
                'role'          => 'user',
                'resource'      => 'roleAccess:listAll',
                'access'        => 0x04
            ]
        ];

        $table = $this->table('identities');
        $table
            ->insert($identitiesData)
            ->save();

        $table = $this->table('users');
        $table
            ->insert($usersData)
            ->save();

        $table = $this->table('role_access');
        $table
            ->insert($roleAccessData)
            ->save();
    }
}
