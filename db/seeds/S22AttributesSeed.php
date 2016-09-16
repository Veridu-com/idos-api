<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S22AttributesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'user1Attribute1',
                'value'      => 'value-1',
                'support'    => 1.2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'user1Attribute2',
                'value'      => 'value-2',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'user2Attribute1',
                'value'      => 'value-3',
                'support'    => 1.4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'user2Attribute2',
                'value'      => 'value-4',
                'support'    => 1.5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 2,
                'name'       => 'user2Attribute3',
                'value'      => 'value-5',
                'support'    => 1.6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $attributes = $this->table('attributes');
        $attributes
            ->insert($data)
            ->save();
    }
}
