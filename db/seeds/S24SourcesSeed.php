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
                'user_id'    => 1,
                'name'       => 'source-2',
                'ipaddr'     => '192.168.0.2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'name'       => 'source-3',
                'ipaddr'     => '192.168.0.3',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'name'       => 'source-4',
                'ipaddr'     => '192.168.0.4',
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
