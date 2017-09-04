<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S25FlagsSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'creator'    => 1,
                'slug'       => 'first-name-mismatch',
                'attribute'  => 'first-name',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'slug'       => 'last-name-mismatch',
                'attribute'  => 'last-name',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'slug'       => 'last-name-mismatch',
                'attribute'  => 'last-name',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
        ];

        $flags = $this->table('flags');
        $flags
            ->insert($data)
            ->save();
    }
}
