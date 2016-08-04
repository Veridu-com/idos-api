<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S00ServicesSeed extends AbstractSeed {
    public function run() {
        $now  = date('Y-m-d H:i:s');

        $servicesData = [
            [
                'name' => 'email',
                'slug' => 'email',
            ],
            [
                'name' => 'sms',
                'slug' => 'sms'
            ]
        ];

        $table = $this->table('services');
        $table
            ->insert($servicesData)
            ->save();

    }
}
