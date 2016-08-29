<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S24SourcesSeed extends AbstractSeed {
    private function seedSources() {
        $data = [
            [
                'user_id'    => 1,
                'name'       => 'source-1',
                'ipaddr'     => '192.168.0.1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'name'       => 'source-2',
                'ipaddr'     => '192.168.0.2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'name'       => 'source-3',
                'ipaddr'     => '192.168.0.3',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'name'       => 'source-4',
                'ipaddr'     => '192.168.0.4',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $sources = $this->table('sources');
        $sources
            ->insert($data)
            ->save();
    }

    private function seedMapped() {
        $data = [
            [
                'source_id'  => 1,
                'name'       => 'source-1-mapped-1',
                'value'      => 'value-1',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 1,
                'name'       => 'source-1-mapped-2',
                'value'      => 'value-2',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 3,
                'name'       => 'source-3-mapped-1',
                'value'      => 'value-3',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 3,
                'name'       => 'source-3-mapped-2',
                'value'      => 'value-32',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 4,
                'name'       => 'source-4-mapped-1',
                'value'      => 'value-4',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $mapped = $this->table('mapped');
        $mapped
            ->insert($data)
            ->save();
    }

    private function seedDigested() {
        $data = [
            [
                'source_id'  => 1,
                'name'       => 'source-1-digested-1',
                'value'      => 'value-1',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 1,
                'name'       => 'source-1-digested-2',
                'value'      => 'value-2',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 3,
                'name'       => 'source-3-digested-1',
                'value'      => 'value-3',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 3,
                'name'       => 'source-3-digested-2',
                'value'      => 'value-32',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'source_id'  => 4,
                'name'       => 'source-4-digested-1',
                'value'      => 'value-4',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $digested = $this->table('digested');
        $digested
            ->insert($data)
            ->save();
    }

    public function run() {
        $this->seedSources();
        $this->seedMapped();
        $this->seedDigested();
    }
}
