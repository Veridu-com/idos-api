<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S25GatesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'          => 1,
                'creator'          => 1,
                'name'             => 'firstName',
                'slug'             => 'firstname',
                'pass'             => true,
                'confidence_level' => 'low',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => null
            ],
            [
                'user_id'          => 1,
                'creator'          => 1,
                'name'             => 'middleName',
                'slug'             => 'middlename',
                'pass'             => 'f',
                'confidence_level' => 'medium',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => null
            ],
            [
                'user_id'          => 1,
                'creator'          => 1,
                'name'             => 'lastName',
                'slug'             => 'lastname',
                'confidence_level' => 'high',
                'pass'             => 'f',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => null
            ],
        ];

        $gates = $this->table('gates');
        $gates
            ->insert($data)
            ->save();
    }
}
