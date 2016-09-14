<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */
use Phinx\Seed\AbstractSeed;

class S24SourcesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'name'       => 'source-1',
                'ipaddr'     => '192.168.0.1',
                'tags'       => json_encode(['otp_check' => 'email']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id' => 1,
                'name'    => 'source-2',
                'ipaddr'  => '192.168.0.2',
                'tags'    => json_encode(
                    [
                        'profile_id'   => 1234567890,
                        'access_token' => md5('x') // 9dd4e461268c8034f5c8564e155c67a6
                    ]
                ),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id' => 2,
                'name'    => 'source-3',
                'ipaddr'  => '192.168.0.3',
                'tags'    => json_encode(
                    [
                        'profile_id'   => 9876543210,
                        'access_token' => md5('y') // 415290769594460e2e485922904f345d
                    ]
                ),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id' => 2,
                'name'    => 'source-4',
                'ipaddr'  => '192.168.0.4',
                'tags'    => json_encode(
                    [
                        'profile_id'   => 1234509876,
                        'access_token' => md5('z') // fbade9e36a3f36d3d676c1b808451dd7
                    ]
                ),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ]
        ];

        $sources = $this->table('sources');
        $sources
            ->insert($data)
            ->save();
    }
}
