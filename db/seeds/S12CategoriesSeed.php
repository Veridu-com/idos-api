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
                'name' => 'First name',
                'slug' => 'first-name',
                'type' => 'attribute', 
                'description' => 'First name of an user.',
                'service_id' => 1
            ],
            [
                'name' => 'Last name',
                'slug' => 'last-name',
                'type' => 'attribute', 
                'description' => 'Last name of an user.',
                'service_id' => 1
            ],
            [
                'name' => 'Middle name',
                'slug' => 'middle-name',
                'type' => 'attribute', 
                'description' => 'Middle name of an user.',
                'service_id' => 1
            ],

            // Warnings
            [
                'name' => 'First name mismatch',
                'slug' => 'first-name-mismatch',
                'type' => 'warning', 
                'description' => 'First name mismatch of an user.',
                'service_id' => 1
            ],
            [
                'name' => 'Last name mismatch',
                'slug' => 'last-name-mismatch',
                'type' => 'warning', 
                'description' => 'Last name mismatch of an user.',
                'service_id' => 1
            ],
            [
                'name' => 'Middle name mismatch',
                'slug' => 'middle-name-mismatch',
                'type' => 'warning', 
                'description' => 'Middle name mismatch of an user.',
                'service_id' => 1
            ]

        ];

        $features = 

        $table = $this->table('categories');
        $table
            ->insert($categories)
            ->save();
    }
}