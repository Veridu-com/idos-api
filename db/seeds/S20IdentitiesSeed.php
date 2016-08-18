<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S20IdentitiesSeed extends AbstractSeed {
    public function run() {
        $identitiesData = [
            [
                'public_key'  => md5('hello'), // 5d41402abc4b2a76b9719d911017c592
                'private_key' => md5('world')  // 7d793037a0760186574b0282f2f435e7
            ],
            [
                'public_key'  => md5('world'), // 7d793037a0760186574b0282f2f435e7
                'private_key' => md5('hello')  // 5d41402abc4b2a76b9719d911017c592
            ]
        ];

        $table = $this->table('identities');
        $table
            ->insert($identitiesData)
            ->save();
    }
}
