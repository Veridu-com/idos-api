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
                'url'           => 'https://scraper.idos.io',
                'company_id'    => 1,
                'auth_username' => 'idos',
                'auth_password' => 'secret',
                'public'        => md5('public-1'),
                'private'       => md5('private-1'),
                'listens'       => json_encode(['idos:source.facebook.added']),
                'triggers'      => json_encode(['idos:scraper.facebook.completed']),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS FB Data Mapper',
                'url'           => 'https://data-mapper.idos.io',
                'company_id'    => 1,
                'auth_username' => 'idos',
                'auth_password' => 'secret',
                'public'        => md5('public-2'),
                'private'       => md5('private-2'),
                'listens'       => json_encode(['idos:scraper.facebook.completed']),
                'triggers'      => json_encode(['idos:data-mapper.facebook.completed']),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Overall Model',
                'url'           => 'https://overall.idos.io',
                'company_id'    => 1,
                'auth_username' => 'idos',
                'auth_password' => 'secret',
                'public'        => md5('public-3'),
                'private'       => md5('private-3'),
                // Why wildcard again?? 
                'listens'  => json_encode(['idos:feature-extractor.facebook.completed', 'idos:feature-extractor.twitter.completed', 'idos:feature-extractor.linkedin.completed']),
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
