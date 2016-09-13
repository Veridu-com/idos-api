<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S12HooksSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'credential_id' => 1,
                'trigger'       => 'company.create',
                'url'           => 'http://example.com/callback-create.php',
                'subscribed'    => true,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ],
            [
                'credential_id' => 1,
                'trigger'       => 'company.update',
                'url'           => 'http://example.com/callback-update.php',
                'subscribed'    => true,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ],
            [
                'credential_id' => 2,
                'trigger'       => 'company.deleteAll',
                'url'           => 'http://example.com/callback-delete-all.php',
                'subscribed'    => true,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]
        ];

        $table = $this->table('hooks');
        $table
            ->insert($data)
            ->save();
    }
}
