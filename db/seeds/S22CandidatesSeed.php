<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

class S22CandidatesSeed extends Db\AbstractExtendedSeed {
    public function run() {
        $now  = date('Y-m-d H:i:s');
        $data = [
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'firstName',
                'value'      => $this->lock('John'),
                'support'    => 0.8,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'firstName',
                'value'      => $this->lock('Johnny'),
                'support'    => 0.2,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'middleName',
                'value'      => $this->lock('Ross'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'lastName',
                'value'      => $this->lock('Doe'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'cityName',
                'value'      => $this->lock('Seattle'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'countryName',
                'value'      => $this->lock('United States'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'email',
                'value'      => $this->lock('john.doe@myserver.com'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'birthDay',
                'value'      => $this->lock('13'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'birthMonth',
                'value'      => $this->lock('10'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'birthYear',
                'value'      => $this->lock('1985'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'attribute'  => 'phone',
                'value'      => $this->lock('7345551212'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'firstName',
                'value'      => $this->lock('Janis'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'middleName',
                'value'      => $this->lock('Lyn'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'lastName',
                'value'      => $this->lock('Joplin'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'cityName',
                'value'      => $this->lock('Port Arthur'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'countryName',
                'value'      => $this->lock('United States'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'email',
                'value'      => $this->lock('janis.joplin@myserver.com'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'birthDay',
                'value'      => $this->lock('19'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'birthMonth',
                'value'      => $this->lock('1'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'birthYear',
                'value'      => $this->lock('1943'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'attribute'  => 'phone',
                'value'      => $this->lock('(734) 5551212'),
                'support'    => 1.0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'creator'    => 2,
                'attribute'  => 'firstName',
                'value'      => $this->lock('Cássio'),
                'support'    => 0.0,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];

        $candidates = $this->table('candidates');
        $candidates
            ->insert($data)
            ->save();
    }
}
