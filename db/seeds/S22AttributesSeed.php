<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S22AttributesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'first-name',
                'value'      => 'John',
                'support'    => 1.2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'first-name',
                'value'      => 'Johnny',
                'support'    => 1.0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'middle-name',
                'value'      => 'Ross',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'last-name',
                'value'      => 'Doe',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'city-name',
                'value'      => 'Seattle',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'country-name',
                'value'      => 'United States',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'email',
                'value'      => 'john.doe@myserver.com',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'birth-day',
                'value'      => '13',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'birth-month',
                'value'      => '10',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'birth-year',
                'value'      => '1985',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 1,
                'creator'    => 1,
                'name'       => 'phone',
                'value'      => '7345551212',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'first-name',
                'value'      => 'Janis',
                'support'    => 1.4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'middle-name',
                'value'      => 'Lyn',
                'support'    => 1.4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'last-name',
                'value'      => 'Joplin',
                'support'    => 1.5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'city-name',
                'value'      => 'Port Arthur',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'country-name',
                'value'      => 'United States',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'email',
                'value'      => 'janis.joplin@myserver.com',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'birth-day',
                'value'      => '19',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'birth-month',
                'value'      => '1',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'birth-year',
                'value'      => '1943',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 1,
                'name'       => 'phone',
                'value'      => '(734) 5551212',
                'support'    => 1.3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id'    => 2,
                'creator'    => 2,
                'name'       => 'first-name',
                'value'      => 'Cássio',
                'support'    => 1.6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $attributes = $this->table('attributes');
        $attributes
            ->insert($data)
            ->save();
    }
}
