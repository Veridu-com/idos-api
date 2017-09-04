<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

class S11SettingsSeed extends Db\AbstractExtendedSeed {
    public function run() {
        $faker = Faker\Factory::create();

        $data = [];
        $now  = date('Y-m-d H:i:s');

        for ($i = 0; $i < 35; $i++) {
            $data = [
                [
                    'company_id' => random_int(1, 2),
                    'section'    => $faker->countryCode,
                    'property'   => $faker->word . '' . $i,
                    'value'      => $this->lock($faker->colorName),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.amazon.key',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.amazon.secret',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.facebook.key',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.facebook.secret',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.google.key',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.google.secret',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.linkedin.key',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.linkedin.secret',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.paypal.key',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.paypal.secret',
                    'value'      => $this->lock(''),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.twitter.key',
                    'value'      => $this->lock('***REMOVED***'),
                    'created_at' => $now,
                    'updated_at' => null
                ],
                [
                    'company_id' => 1,
                    'section'    => 'AppTokens',
                    'property'   => '4c9184f37cff01bcdc32dc486ec36961.twitter.secret',
                    'value'      => $this->lock('***REMOVED***'),
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
            'value'      => $this->lock('Veridu Dashboard'),
            'created_at' => $now
        ];

        // company recommendation
        $data[] = [
            'company_id' => 1,
            'section'    => 'recommendation',
            'property'   => 'ruleset',
            'value'      => $this->lock(
                json_encode(
                    [
                        [
                            'tag'   => 'Default rule',
                            'tests' => [
                                [
                                    'tag'              => 'default test #1',
                                    'category'         => 'gate',
                                    'slug'             => 'nochargebackgate',
                                    'confidence_level' => 'medium'
                                ]
                            ]
                        ]
                    ]
                )
            ),
            'created_at' => $now
        ];

        $settings = $this->table('settings');
        $settings
            ->insert($data)
            ->save();
    }
}
