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
                'identity_id' => 1,
                'name'    => 'High-end customer',
                'slug'    => 'high-end-customer'
            ],
            [
                'user_id' => 1,
                'identity_id' => 1,
                'name'    => 'Partner',
                'slug'    => 'partner'
            ],

            [
                'user_id' => 2,
                'identity_id' => 1,
                'name'    => 'Low-end customer',
                'slug'    => 'low-end-customer'
            ],
            [
                'user_id' => 1,
                'identity_id' => 1,
                'name'    => 'Repeat customer',
                'slug'    => 'repeat-customer'
            ],
        ];

        $table = $this->table('tags');
        $table
            ->insert($data)
            ->save();
    }
}
