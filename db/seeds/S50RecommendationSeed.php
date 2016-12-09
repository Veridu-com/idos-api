<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S50RecommendationSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'user_id'    => 1,
                'creator'    => 1,
                'result'     => 'pass',
                'passed'     => json_encode([
                    [
                        'tag'       => 'Rule 1',
                        'connector' => 'AND',
                        'passed'    => [
                            [
                                'tag'              => 'default test #1 for rule #1',
                                'category'         => 'gate',
                                'slug'             => 'nochargebackgate-medium',
                                'confidence_level' => 'medium',
                                'pass'             => true
                            ]
                        ],
                        'failed'    => [

                        ]
                    ],
                    [
                        'tag'       => 'Rule 2',
                        'connector' => 'OR',
                        'passed'    => [
                            
                        ],
                        'failed'    => [
                            [
                                'tag'              => 'default test #1 for rule #2',
                                'category'         => 'score',
                                'name'             => 'user-1-score-1',
                                'cmp_value'        => 0.7,
                                'operator'         => '>='
                            ]
                        ]
                    ]
                ]),
                'failed'     => json_encode([
                    
                ]),
                'created_at' => date('Y-m-d H:i:s')
            ],

            [
                'user_id'    => 2,
                'creator'    => 1,
                'result'     => 'fail',
                'passed'     => json_encode([
                    
                ]),
                'failed'     => json_encode([
                    [
                        'tag'       => 'Rule 1',
                        'connector' => 'OR',
                        'passed'    => [

                        ],
                        'failed'    => [
                            [
                                'tag'              => 'default test #1 for rule #1',
                                'category'         => 'gate',
                                'slug'             => 'nochargebackgate-medium',
                                'confidence_level' => 'medium',
                                'pass'             => true
                            ]
                        ]
                    ],
                    [
                        'tag'       => 'Rule 2',
                        'connector' => 'AND',
                        'passed'    => [
                            
                        ],
                        'failed'    => [
                            [
                                'tag'              => 'default test #1 for rule #2',
                                'category'         => 'score',
                                'name'             => 'user-2-score-1',
                                'cmp_value'        => 0.7,
                                'operator'         => '>='
                            ]
                        ]
                    ]
                ]),
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        $recommendations = $this->table('recommendations');
        $recommendations
            ->insert($data)
            ->save();
    }
}
