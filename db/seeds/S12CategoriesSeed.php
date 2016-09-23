<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S12CategoriesSeed extends AbstractSeed {
    public function run() {

        $categories = [
            // Attributes
            [
                'name'        => 'First name',
                'slug'        => 'first-name',
                'type'        => 'attribute',
                'description' => 'First name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Last name',
                'slug'        => 'last-name',
                'type'        => 'attribute',
                'description' => 'Last name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Middle name',
                'slug'        => 'middle-name',
                'type'        => 'attribute',
                'description' => 'Middle name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth day',
                'slug'        => 'birth-day',
                'type'        => 'attribute',
                'description' => 'Birth day of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth month',
                'slug'        => 'birth-month',
                'type'        => 'attribute',
                'description' => 'Birth month of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'City name',
                'slug'        => 'city-name',
                'type'        => 'attribute',
                'description' => 'City name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Country name',
                'slug'        => 'country-name',
                'type'        => 'attribute',
                'description' => 'Country name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Email',
                'slug'        => 'email',
                'type'        => 'attribute',
                'description' => 'Email of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Gender',
                'slug'        => 'gender',
                'type'        => 'attribute',
                'description' => 'Gender of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Phone',
                'slug'        => 'phone',
                'type'        => 'attribute',
                'description' => 'Phone of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Street address',
                'slug'        => 'street-address',
                'type'        => 'attribute',
                'description' => 'Street address of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Zipcode',
                'slug'        => 'zipcode',
                'type'        => 'attribute',
                'description' => 'Zipcode of a user.',
                'service_id'  => 1
            ],

            // Warnings
            [
                'name'        => 'First name mismatch',
                'slug'        => 'first-name-mismatch',
                'type'        => 'warning',
                'description' => 'First name mismatch of an user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Last name mismatch',
                'slug'        => 'last-name-mismatch',
                'type'        => 'warning',
                'description' => 'Last name mismatch of an user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Middle name mismatch',
                'slug'        => 'middle-name-mismatch',
                'type'        => 'warning',
                'description' => 'Middle name mismatch of an user.',
                'service_id'  => 1
            ]

        ];

        $features = $table = $this->table('categories');
        $table
            ->insert($categories)
            ->save();
    }
}
