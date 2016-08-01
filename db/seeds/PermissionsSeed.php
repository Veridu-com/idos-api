<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */
use Phinx\Seed\AbstractSeed;

class PermissionsSeed extends AbstractSeed {
    public function run() {
        $faker      = Faker\Factory::create();
        $routeFiles = glob(__DIR__ . '/../../app/Route/*.php');

        $names = [];
        foreach ($routeFiles as $file) {
            $classname =  sprintf('App\\Route\\%s', basename($file, '.php'));
            if (strpos($classname, 'Interface') === false) {
                foreach ($classname::getPublicNames() as $name) {
                    $names[] = $name;
                }
            }
        }

        $data = [];
        $now  = date('Y-m-d H:i:s');
        
        foreach ($names as $routeName) {
            $data[] = [
                'company_id'    => 1,           // Company #2 wont have any permssion
                'route_name'    => $routeName,
                'created_at'    => $now
            ];
        }

        $permissions = $this->table('permissions');
        $permissions
            ->insert($data)
            ->save();
    }
}
