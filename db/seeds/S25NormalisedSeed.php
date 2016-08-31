<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S25NormalisedSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'source_id'  => 1,
                'name'       => 'source1Normalised1',
                'value'      => 'value-1',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 1,
                'name'       => 'source1Normalised2',
                'value'      => 'value-2',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 3,
                'name'       => 'source3Normalised1',
                'value'      => 'value-3',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 3,
                'name'       => 'source3Normalised2',
                'value'      => 'value-32',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 4,
                'name'       => 'source4Normalised1',
                'value'      => 'value-4',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $normalised = $this->table('normalised');
        $normalised
            ->insert($data)
            ->save();
    }
}
