<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class RolesSeed extends AbstractSeed {
    
    public function run() {
        $roles = [
            ['name' => 'company'],
            ['name' => 'company.owner'],
            ['name' => 'company.admin'],
            ['name' => 'company.member'],
            ['name' => 'user'],
            ['name' => 'guest']
        ];

        $table = $this->table('roles');
        $table
            ->insert($roles)
            ->save();

        return $roles;

    }

}