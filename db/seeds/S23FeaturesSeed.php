<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S23FeaturesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'name'       => 'Friend count',
                'slug'       => 'friend-count',
                'value'      => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'name'       => 'Relative count',
                'slug'       => 'relative-count',
                'value'      => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'name'       => 'Friend count',
                'slug'       => 'friend-count',
                'value'      => '10',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'name'       => 'Relative count',
                'slug'       => 'relative-count',
                'value'      => '5',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
        ];

        $features = $this->table('features');
        $features
            ->insert($data)
            ->save();
    }
}
