<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S25WarningsSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'warning one',
                'slug'       => 'warning-one',
                'reference'  => 'firstName',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'warning two',
                'slug'       => 'warning-two',
                'reference'  => 'lastName',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
        ];

        $warnings = $this->table('warnings');
        $warnings
            ->insert($data)
            ->save();
    }
}
