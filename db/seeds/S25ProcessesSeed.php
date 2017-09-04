<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S25ProcessesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'source_id'  => 1,
                'name'       => 'idos:verification',
                'event'      => 'idos:source.facebook.created',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'name'       => 'Some random process for the user',
                'source_id'  => null,
                'event'      => 'idos:source.sms.verified',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ]
        ];

        $processes = $this->table('processes');
        $processes
            ->insert($data)
            ->save();

        $data = [
            [
                'process_id' => 1,
                'creator'    => 1,
                'name'       => 'Task one',
                'event'      => 'user:created',
                'running'    => true,
                'message'    => 'Messsage',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'process_id' => 1,
                'creator'    => 1,
                'name'       => 'Task two',
                'event'      => 'user:created',
                'success'    => true,
                'message'    => 'Messsage',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'process_id' => 2,
                'creator'    => 1,
                'name'       => 'Task three',
                'event'      => 'user:created',
                'running'    => true,
                'message'    => 'Messsage',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'process_id' => 2,
                'creator'    => 1,
                'name'       => 'Task four',
                'event'      => 'user:created',
                'success'    => true,
                'message'    => 'Messsage',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
        ];

        $tasks = $this->table('tasks');
        $tasks
            ->insert($data)
            ->save();
    }
}
