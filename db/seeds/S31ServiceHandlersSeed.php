<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S31ServiceHandlersSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'company_id'        => 1,
                'service_slug'      => 'email',
                'name'              => 'Veridu email handler',
                'slug'              => 'veridu-email-handler',
                'source'            => 'source',
                'location'          => 'http://localhost:8001',
                'auth_username'     => 'idos',
                'auth_password'     => 'secret',
                'created_at'        => $now
            ],
            [
                'company_id'        => 1,
                'service_slug'      => 'sms',
                'name'              => 'Veridu sms handler',
                'slug'              => 'veridu-sms-handler',
                'source'            => 'source',
                'location'          => 'http://localhost:8001',
                'auth_username'     => 'idos',
                'auth_password'     => 'secret',
                'created_at'        => $now
            ],
        ];

        $service_handlers = $this->table('service_handlers');
        $service_handlers
            ->insert($data)
            ->save();
    }
}
