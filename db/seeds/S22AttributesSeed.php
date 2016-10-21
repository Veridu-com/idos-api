<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S22AttributesSeed extends AbstractSeed {
    public function run() {
        $now  = date('Y-m-d H:i:s');
        $data = [
            [
                'user_id'    => 1,
                'name'       => 'firstName',
                'value'      => 'John',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'lastName',
                'value'      => 'Doe',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'cityName',
                'value'      => 'Seattle',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'countryName',
                'value'      => 'United States',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'email',
                'value'      => 'john.doe@myserver.com',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'birthDay',
                'value'      => '13',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'birthMonth',
                'value'      => '10',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'birthYear',
                'value'      => '1985',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 1,
                'name'       => 'phoneNumber',
                'value'      => '7345551212',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'firstName',
                'value'      => 'Janis',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'middleName',
                'value'      => 'Lyn',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'lastName',
                'value'      => 'Joplin',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'cityName',
                'value'      => 'Port Arthur',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'countryName',
                'value'      => 'United States',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'email',
                'value'      => 'janis.joplin@myserver.com',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'birthDay',
                'value'      => '19',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'birthMonth',
                'value'      => '1',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'birthYear',
                'value'      => '1943',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id'    => 2,
                'name'       => 'phoneNumber',
                'value'      => '(734) 5551212',
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
