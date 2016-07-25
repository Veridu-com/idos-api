<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class RolesSeed extends AbstractSeed {
    public function run() {
        $now  = date('Y-m-d H:i:s');

        $roles = [
            ['name' => 'company',          'created_at'   => $now],
            ['name' => 'company_owner',    'created_at'   => $now],
            ['name' => 'company_admin',    'created_at'   => $now],
            ['name' => 'company_member',   'created_at'   => $now],
            ['name' => 'user',             'created_at'   => $now],
            ['name' => 'guest',            'created_at'   => $now]
        ];

        $table = $this->table('roles');
        $table
            ->insert($roles)
            ->save();
    }
}
