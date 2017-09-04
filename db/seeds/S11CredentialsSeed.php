<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

class S11CredentialsSeed extends Db\AbstractExtendedSeed {
    public function run() {
        $data = [
            [
                'company_id' => 1,
                'name'       => 'My Test Key',
                'slug'       => 'my-test-key',
                'special'    => true,
                'public'     => md5('public'),               // 4c9184f37cff01bcdc32dc486ec36961
                'private'    => $this->lock(md5('private')), // 2c17c6393771ee3048ae34d6b380c5ec
                'production' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'company_id' => 1,
                'name'       => 'My Production Key',
                'slug'       => 'my-production-key',
                'public'     => md5('public5'),               // fc8ce54607854df8b72e7324c8f6aa24
                'private'    => $this->lock(md5('private5')), // 7a2e57ec5305d38463f78b30e46300d8
                'production' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'company_id' => 2,
                'name'       => 'My Test Key',
                'slug'       => 'my-test-key',
                'public'     => md5('public6'),               // 1e772b1e4d57560422e07565600aca48
                'private'    => $this->lock(md5('private6')), // 6dd1e54c33b69238747fc158d06d2514
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
