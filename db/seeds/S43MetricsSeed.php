<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S43MetricsSeed extends AbstractSeed {
    public function run() {
        $count     = 5000;
        $startDate = '2015-11-01 00:00:00';

        for ($i = 0; $i < $count; $i++) {
            $data = [
                [
                    'credential_public' => ['4c9184f37cff01bcdc32dc486ec36961', 'fc8ce54607854df8b72e7324c8f6aa24'][random_int(0, 1)],
                    'endpoint'          => 'profile:source',
                    'action'            => ['created', 'deleted'][random_int(0, 1)],
                    'data'              => json_encode(
                        [
                        'provider'      => ['facebook', 'google', 'yahoo', 'linkedin'][random_int(0, 3)],
                        'sso'           => (bool) random_int(0, 1)
                        ]
                    ),
                    'created_at' => date('Y-m-d H:i:s', random_int(strtotime($startDate), time()))
                ],
            ];

            $this->table('metrics')
                ->insert($data)
                ->save();

            $data = [
                [
                    'credential_public' => ['4c9184f37cff01bcdc32dc486ec36961', 'fc8ce54607854df8b72e7324c8f6aa24'][random_int(0, 1)],
                    'endpoint'          => 'profile:gate',
                    'action'            => ['created', 'deleted'][random_int(0, 1)],
                    'data'              => json_encode(
                        [
                        'name'          => ['Gate One', 'Gate Two'][random_int(0, 1)],
                        'pass'          => (bool) random_int(0, 1)
                        ]
                    ),
                    'created_at' => date('Y-m-d H:i:s', random_int(strtotime($startDate), time()))
                ],
            ];

            $this->table('metrics')
                ->insert($data)
                ->save();
        }
    }
}
