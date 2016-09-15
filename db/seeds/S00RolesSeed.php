<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S00RolesSeed extends AbstractSeed {
    public function run() {
        $roles = [
            ['name' => 'company', 'rank' => 0, 'bit' => 0x01],
            ['name' => 'company.owner', 'rank' => 1, 'bit' => 0x02],
            ['name' => 'company.admin', 'rank' => 2, 'bit' => 0x04],
            ['name' => 'company.member', 'rank' => 3, 'bit' => 0x08],
            ['name' => 'user', 'rank' => 4, 'bit' => 0x016],
            ['name' => 'guest', 'rank' => 5, 'bit' => 0x032]
        ];

        $table = $this->table('roles');
        $table
            ->insert($roles)
            ->save();
    }
}
