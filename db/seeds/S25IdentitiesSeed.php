<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S25IdentitiesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'reference'   => md5('source-2:1234567890'),    // 3c46c03ea31da2546dcc641211716dfb
                'public_key'  => md5('hello'),                  // 5d41402abc4b2a76b9719d911017c592
                'private_key' => md5('world')                   // 7d793037a0760186574b0282f2f435e7
            ],
            [
                'reference'   => md5('source-3:9876543210'),    // 8b07a85d8984afa1f32c937edc413e2f
                'public_key'  => md5('world'),                  // 7d793037a0760186574b0282f2f435e7
                'private_key' => md5('hello')                   // 5d41402abc4b2a76b9719d911017c592
            ],
            [
                'reference'   => md5('source-4:1234509876'),    // 26b0576d102842cdd05241d1db94b64b
                'public_key'  => md5('helwo'),                  // f69450212de194f43bf223708210c6a4
                'private_key' => md5('rldlo')                   // c2dde4aad55b5c17b98f5e85fd7a4953
            ]
        ];

        $table = $this->table('identities');
        $table
            ->insert($data)
            ->save();
    }
}
