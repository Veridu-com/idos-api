<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S23TagsSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id' => 1,
                'name'    => 'User 1 Tag 1',
                'slug'    => 'user-1-tag-1'
            ],
            [
                'user_id' => 1,
                'name'    => 'User 1 Tag 2',
                'slug'    => 'user-1-tag-2'
            ],

            [
                'user_id' => 2,
                'name'    => 'User 2 Tag 1',
                'slug'    => 'user-2-tag-1'
            ],
            [
                'user_id' => 2,
                'name'    => 'User 2 Tag 2',
                'slug'    => 'user-2-tag-2'
            ],
        ];

        $table = $this->table('tags');
        $table
            ->insert($data)
            ->save();
    }
}
