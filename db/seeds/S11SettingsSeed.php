<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S11SettingsSeed extends AbstractSeed {
    public function run() {
        $faker = Faker\Factory::create();

        $data = [];
        $now  = date('Y-m-d H:i:s');

        for ($i = 0; $i < 35; $i++) {
            $data = [
                [
                    'company_id' => mt_rand(1, 2),
                    'section'    => $faker->countryCode,
                    'property'   => $faker->word . '' . $i,
                    'value'      => $faker->colorName,
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.amazon.key',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.amazon.secret',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.facebook.key',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.facebook.secret',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.google.key',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.google.secret',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.linkedin.key',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.linkedin.secret',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.paypal.key',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.paypal.secret',
                    'value'      => '',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.twitter.key',
                    'value'      => '***REMOVED***',
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.twitter.secret',
                    'value'      => '***REMOVED***',
                    'created_at' => $now,
                    'updated_at' => null
                ]
            ];
        }

        // dashboard details
        $data[] = [
            'company_id' => 1,
            'section'    => 'company.details',
            'property'   => 'dashboardName',
            'value'      => 'Veridu Dashboard',
            'created_at' => $now
        ];

        // company recommendation
        $data[] = [
            'company_id' => 1,
            'section'    => 'recommendation',
            'property'   => 'ruleset',
            'value'      => json_encode([
                [
                    'tag'   => 'Default rule',
                    'tests' => [
                        [
                            'tag'              => 'default test #1',
                            'category'         => 'gate',
                            'slug'             => 'nochargebackgate-medium',
                            'confidence_level' => 'medium',
                            'pass'             => true
                        ]
                    ]
                ]
            ]),
            'created_at' => $now
        ];

        $settings = $this->table('settings');
        $settings
            ->insert($data)
            ->save();
    }
}
