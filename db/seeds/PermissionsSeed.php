<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */
use Phinx\Seed\AbstractSeed;

class PermissionsSeed extends AbstractSeed {
    public function run() {
        $faker = Faker\Factory::create();

        $routes = [
            'companies:listAll',
            'companies:createNew',
            'companies:deleteAll',
            'companies:getOne',
            'companies:updateOne',
            'companies:deleteOne'
        ];

        $data = [];
        $now  = date('Y-m-d H:i:s');
        $size = sizeof($routes) - 1;

        foreach ($routes as $route) {
            $data[] = [
                'company_id'    => mt_rand(1, 2),
                'route_name'    => $route,
                'created_at'    => $now
            ];
        }


        $permissions = $this->table('permissions');
        $permissions
            ->insert($data)
            ->save();
    }
}
