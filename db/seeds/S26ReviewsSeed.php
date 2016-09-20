<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S26ReviewsSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'     => 1,
                'warning_id'  => 1,
                'identity_id' => 1,
                'positive'    => 'false',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => null
            ],
            [
                'user_id'     => 1,
                'warning_id'  => 2,
                'identity_id' => 1,
                'positive'    => 'false',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => null
            ]
        ];

        $reviews = $this->table('reviews');
        $reviews
            ->insert($data)
            ->save();
    }
}
