<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S00DaemonsSeed extends AbstractSeed {
    public function run() {
        $now  = date('Y-m-d H:i:s');

        $daemonsData = [
            [
                'name' => 'Scraper',
                'slug' => 'scraper',
            ],
            [
                'name' => 'Feature extractor',
                'slug' => 'feature-extractor'
            ],
            [
                'name' => 'Data mapper',
                'slug' => 'data-mapper'
            ],
            [
                'name' => 'Model',
                'slug' => 'model'
            ]
        ];

        $table = $this->table('daemons');
        $table
            ->insert($daemonsData)
            ->save();

    }
}
