<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S11ServicesSeed extends AbstractSeed {
    public function run() {
        $now  = date('Y-m-d H:i:s');

        $servicesData = [
            [
                'name' => 'idos FB Scraper',
                'url' => 'https://scraper.idos.io',
                'username' => 'idos',
                'company_id' => 2,
                'password' => 'secret',
                'listens' => json_encode([ 'idos:source.facebook.added' ]),
                'triggers' => json_encode([ 'idos:scraper.facebook.completed' ]),
                'enabled' => true,
            ],
            [
                'name' => 'idos FB Data Mapper',
                'url' => 'https://data-mapper.idos.io',
                'username' => 'idos',
                'company_id' => 2,
                'password' => 'secret',
                'listens' => json_encode([ 'idos:scraper.facebook.completed' ]),
                'triggers' => json_encode([ 'idos:data-mapper.facebook.completed' ]),
                'enabled' => true,
            ],
            [
                'name' => 'idos Overall Model',
                'url' => 'https://overall.idos.io',
                'username' => 'idos',
                'company_id' => 2,
                'password' => 'secret',
                // Why wildcard again?? 
                'listens' => json_encode([ 'idos:feature-extractor.facebook.completed', 'idos:feature-extractor.twitter.completed', 'idos:feature-extractor.linkedin.completed' ]),
                'triggers' => json_encode([ 'idos:overall.completed' ]),
                'enabled' => true,
            ],
        ];

        $table = $this->table('services');
        $table
            ->insert($servicesData)
            ->save();

    }
}
