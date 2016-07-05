<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class CompaniesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'name'        => 'Veridu Ltd',
                'slug'        => 'veridu-ltd',
                'public_key'  => md5('veridu-ltd'), // 8b5fe9db84e338b424ed6d59da3254a0
                'private_key' => md5('dtl-udirev'), // 4e37dae79456985ae0d27a67639cf335
                'personal'    => 0,
                'parent_id'   => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'App Deck',
                'slug'        => 'app-deck',
                'public_key'  => md5('app-deck'), // 22b5a38c15e3da2e18a0a4bf70262456
                'private_key' => md5('kced-ppa'), // c8dc9b7ba43297d44b0a8776018feb5a
                'personal'    => 0,
                'parent_id'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ]
        ];

        $companies = $this->table('companies');
        $companies
            ->insert($data)
            ->save();
    }
}
