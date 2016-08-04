<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S22MembersSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'company_id' => 1,
                'user_id'   => 2,
                'role'       => 'owner',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $members = $this->table('members');
        $members
            ->insert($data)
            ->save();
    }
}
