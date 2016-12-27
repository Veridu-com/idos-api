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
                'name'             => 'firstNameGate',
                'slug'             => 'firstname-low',
                'confidence_level' => 'low',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => null
            ],
            [
                'user_id'          => 1,
                'creator'          => 1,
                'name'             => 'noChargebackGate',
                'slug'             => 'nochargebackgate',
                'confidence_level' => 'high',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => null
            ],
            [
                'user_id'          => 1,
                'creator'          => 1,
                'name'             => 'lastNameGate',
                'slug'             => 'lastname-high',
                'confidence_level' => 'high',
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
