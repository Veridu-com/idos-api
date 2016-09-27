<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S30ServiceHandlersSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'company_id' => 1,
                'service_id' => 1, // scraper
                'listens'    => json_encode(['idos:source.facebook.created']),
                'created_at' => $now
            ],
            [
                'company_id' => 1,
                'service_id' => 2, // raw created
                'listens'    => json_encode([
                    'idos:raw.facebook.created',
                    'idos:raw.facebook.updated'
                    ]),
                'created_at' => $now
            ]
        ];

        $service_handlers = $this->table('service_handlers');
        $service_handlers
            ->insert($data)
            ->save();
    }
}
