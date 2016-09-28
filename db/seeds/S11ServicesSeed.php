<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S11ServicesSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $servicesData = [
            [
                'name'          => 'idOS FB Scraper',
                // 'url'           => 'https://scraper.idos.io',
                'url'           => 'https://localhost:8081/index.php/1.0/scrape',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-1'), // ef970ffad1f1253a2182a88667233991
                'private'       => md5('private-1'), // 213b83392b80ee98c8eb2a9fed9bb84d
                'listens'       => json_encode(['idos:source.facebook.created']),
                'triggers'      => json_encode(['idos:raw.facebook.created']),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS FB Feature Extractor',
                // 'url'           => 'https://feature-extractor.idos.io',
                'url'           => 'https://localhost:8082/index.php/1.0/feature',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-2'), // 8c178e650645a1f2a0c7de98757373b6
                'private'       => md5('private-2'), // e603de4692c2179446a96374bce86ce6
                'listens'       => json_encode(['idos:raw.facebook.created']),
                'triggers'      => json_encode(['idos:feature.facebook.completed']),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Overall Model',
                // 'url'           => 'https://overall.idos.io',
                'url'           => 'https://localhost:8083/skynet-web/rest/overall-cs-nb',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-3'), // 043578887a8013e3805a789927b0fbf2
                'private'       => md5('private-3'), // 36bf101e92f80f4033b588e6ce4a746b
                'listens'       => json_encode(
                    [
                        'idos:feature.facebook.created',
                        'idos:feature.twitter.created',
                        'idos:feature.linkedin.created'
                    ]
                ),
                'triggers' => json_encode(['idos:overall.completed']),
                'enabled'  => true,
            ],
        ];

        $table = $this->table('services');
        $table
            ->insert($servicesData)
            ->save();

    }
}
