<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S00RolesSeed extends AbstractSeed {
    public function run() {
        $roles = [
            ['name' => 'company', 'rank' => 0],
            ['name' => 'company.owner', 'rank' => 1],
            ['name' => 'company.admin', 'rank' => 2],
            ['name' => 'company.member', 'rank' => 3],
            ['name' => 'user', 'rank' => 0],
            ['name' => 'guest', 'rank' => 1]
        ];

        $table = $this->table('roles');
        $table
            ->insert($roles)
            ->save();
    }
}
