<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S23TagsSeed extends AbstractSeed {
    public function run() {
        $tagsData = [
            [
                'user_id' => 1,
                'name'    => 'user-1-tag-1'
            ],
            [
                'user_id' => 1,
                'name'    => 'user-1-tag-2'
            ],

            [
                'user_id' => 2,
                'name'    => 'user-2-tag-1'
            ],
            [
                'user_id' => 2,
                'name'    => 'user-2-tag-2'
            ],
        ];

        $table = $this->table('tags');
        $table
            ->insert($tagsData)
            ->save();
    }
}
