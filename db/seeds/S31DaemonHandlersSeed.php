<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S31DaemonHandlersSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'company_id'        => 1,
                'daemon_slug'       => 'model',
                'runlevel'          => 1,
                'step'              => 'build',
                'name'              => 'Veridu First Name model',
                'slug'              => 'veridu-first-name-model',
                'source'            => 'source',
                'location'          => 'http://localhost:8001',
                'auth_username'     => 'idos',
                'auth_password'     => 'secret',
                'created_at'        => $now
            ],
            [
                'company_id'        => 1,
                'daemon_slug'       => 'data-mapper',
                'runlevel'          => 2,
                'step'              => 'build',
                'name'              => 'Veridu Data Mapper',
                'slug'              => 'veridu-data-mapper',
                'source'           => 'source',
                'location'          => 'http://localhost:8001',
                'auth_username'     => 'idos',
                'auth_password'     => 'secret',
                'created_at'        => $now
            ],
        ];

        $daemon_handlers = $this->table('daemon_handlers');
        $daemon_handlers
            ->insert($data)
            ->save();
    }
}
