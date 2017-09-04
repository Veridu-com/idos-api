<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S41InvitationsSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'name'          => 'Flavio Heleno',
                'email'         => 'flavio@veridu.com',
                'company_id'    => 1,
                'role'          => 'company.owner',
                'credential_id' => 1,
                'member_id'     => null,
                'creator_id'    => null,
                'expires'       => date('Y-m-d H:i:s', strtotime('now + 1 days')),
                'hash'          => md5('cool-hash'), // cbff30de456ce61ccb7c1021c4dbf8b2
                'voided'        => 0,
                'created_at'    => $now
            ]
        ];

        $invitations = $this->table('invitations');
        $invitations
            ->insert($data)
            ->save();
    }
}
