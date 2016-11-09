<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S43MetricsSeed extends AbstractSeed {
    public function run() {
        $count = 5000;
        $startDate = '2015-11-01 00:00:00';

        for ($i = 0; $i < $count; $i++) {
            $data = [
                [
                    'endpoint'      => 'profile:source',
                    'action'        => ['created', 'deleted'][random_int(0, 1)],
                    'data'          => json_encode([
                        'credential_id' => 1,
                        'provider'      => ['facebook', 'google', 'yahoo', 'linkedin'][random_int(0, 3)],
                        'sso'           => (bool) random_int(0, 1)
                    ]),
                    'created_at' => date('Y-m-d H:i:s', random_int(strtotime($startDate), time()))
                ],
            ];

            $this->table('metrics')
                ->insert($data)
                ->save();

            $data = [
                [
                    'endpoint'      => 'profile:gate',
                    'action'        => ['created', 'deleted'][random_int(0, 1)],
                    'data'          => json_encode([
                        'credential_id' => 1,
                        'name'          => ['Gate One', 'Gate Two'][random_int(0, 1)],
                        'pass'          => (bool) random_int(0, 1)
                    ]),
                    'created_at' => date('Y-m-d H:i:s', random_int(strtotime($startDate), time()))
                ],
            ];

            $this->table('metrics')
                ->insert($data)
                ->save();
        }
    }
}
