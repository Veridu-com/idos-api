<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S22ReferencesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'name'       => 'user1Reference1',
                'value'      => 'value-1',
                'ipaddr'     => '127.0.0.1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'name'       => 'user1Reference2',
                'value'      => 'value-2',
                'ipaddr'     => '127.0.0.1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'name'       => 'user2Reference1',
                'value'      => 'value-3',
                'ipaddr'     => '127.0.0.1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'name'       => 'user2Reference2',
                'value'      => 'value-4',
                'ipaddr'     => '127.0.0.1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'name'       => 'user2Reference3',
                'value'      => 'value-5',
                'ipaddr'     => '127.0.0.1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $references = $this->table('references');
        $references
            ->insert($data)
            ->save();
    }
}
