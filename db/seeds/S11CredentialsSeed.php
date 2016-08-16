<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S11CredentialsSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'company_id' => 1,
                'name'       => 'My Test Key',
                'slug'       => 'my-test-key',
                'public'     => md5('public'),      // 4c9184f37cff01bcdc32dc486ec36961
                'private'    => md5('private'),     // 2c17c6393771ee3048ae34d6b380c5ec
                'production' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'company_id' => 1,
                'name'       => 'My Test Key',
                'slug'       => 'my-test-key',
                'public'     => md5('public5'),     // fc8ce54607854df8b72e7324c8f6aa24
                'private'    => md5('private5'),    // 7a2e57ec5305d38463f78b30e46300d8
                'production' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $credentials = $this->table('credentials');
        $credentials
            ->insert($data)
            ->save();
    }
}