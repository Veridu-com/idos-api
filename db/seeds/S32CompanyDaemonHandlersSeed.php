<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S32CompanyDaemonHandlersSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'company_id'            => 1,
                'daemon_handler_id'    => 1,
                'created_at'            => $now,
                'updated_at'            => $now
            ],
            [
                'company_id'            => 1,
                'daemon_handler_id'    => 2,
                'created_at'            => $now,
                'updated_at'            => $now
            ],
        ];

        $company_daemon_handlers = $this->table('company_daemon_handlers');
        $company_daemon_handlers
            ->insert($data)
            ->save();
    }
}
