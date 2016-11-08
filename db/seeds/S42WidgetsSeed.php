<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S42WidgetsSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'hash'          => 'abcd1234',
                'label'         => 'My Cool Widget',
                'type'          => 'embedded-widget',
                'config'        => json_encode(['gates' => 'gate_1']),
                'creator_id'    => 1,
                'company_id'    => 1,
                'credential_id' => 1
            ],
        ];

        $widgets = $this->table('widgets');
        $widgets
            ->insert($data)
            ->save();
    }
}
