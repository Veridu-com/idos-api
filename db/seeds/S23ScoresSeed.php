<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S23ScoresSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'firstName',
                'name'       => 'user-1-score-1',
                'value'      => 1.2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'user-1-score-2',
                'attribute'  => 'firstName',
                'value'      => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'user-2-score-1',
                'attribute'  => 'lastName',
                'value'      => 1.4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'user-2-score-2',
                'attribute'  => 'lastName',
                'value'      => 1.0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 2,
                'name'       => 'user-2-score-3',
                'attribute'  => 'middleName',
                'value'      => 1.6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $scores = $this->table('scores');
        $scores
            ->insert($data)
            ->save();
    }
}
