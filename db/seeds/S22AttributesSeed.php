<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

class S22AttributesSeed extends Db\AbstractExtendedSeed {
    public function run() {
        $now  = date('Y-m-d H:i:s');
        $data = [
            [
                'user_id'    => 1,
                'name'       => 'firstName',
                'value'      => $this->lock('John'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'fullName',
                'value'      => $this->lock('John Doe'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'name'       => 'lastName',
                'value'      => $this->lock('Doe'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'fullName',
                'value'      => $this->lock('Janis Joplin'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'name'       => 'cityName',
                'value'      => $this->lock('Seattle'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'countryName',
                'value'      => $this->lock('United States'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'email',
                'value'      => $this->lock('john.doe@myserver.com'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'birthDay',
                'value'      => $this->lock('13'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'birthMonth',
                'value'      => $this->lock('10'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'birthYear',
                'value'      => $this->lock('1985'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'phoneNumber',
                'value'      => $this->lock('7345551212'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'firstName',
                'value'      => $this->lock('Janis'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'middleName',
                'value'      => $this->lock('Lyn'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'lastName',
                'value'      => $this->lock('Joplin'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'cityName',
                'value'      => $this->lock('Port Arthur'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'countryName',
                'value'      => $this->lock('United States'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'email',
                'value'      => $this->lock('janis.joplin@myserver.com'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'birthDay',
                'value'      => $this->lock('19'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'birthMonth',
                'value'      => $this->lock('1'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'birthYear',
                'value'      => $this->lock('1943'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'phoneNumber',
                'value'      => $this->lock('(734) 5551212'),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];

        $attributes = $this->table('attributes');
        $attributes
            ->insert($data)
            ->save();
    }
}
