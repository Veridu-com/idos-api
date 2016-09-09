<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S25FeaturesSeed extends AbstractSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'source_id'  => 1,
                'name'       => 'birthYear',
                'creator'    => 'skynet',
                'type'       => 'integer',
                'value'      => '1992',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source_id'  => 1,
                'name'       => 'birthMonth',
                'creator'    => 'skynet',
                'type'       => 'integer',
                'value'      => '5',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source_id'  => 2,
                'name'       => 'birthDay',
                'creator'    => 'skynet',
                'type'       => 'integer',
                'value'      => '22',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source_id'  => 2,
                'name'       => 'numOfFriends',
                'creator'    => 'skynet',
                'type'       => 'integer',
                'value'      => '4',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source_id'  => 3,
                'name'       => 'isVerified',
                'creator'    => 'skynet',
                'type'       => 'boolean',
                'value'      => 'false',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'source_id'  => 3,
                'name'       => 'isCelebrity',
                'creator'    => 'skynet',
                'type'       => 'boolean',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source_id'  => null,
                'name'       => 'submittedName',
                'creator'    => 'hal9000',
                'type'       => 'string',
                'value'      => 'John Doe',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'source_id'  => null,
                'name'       => 'submittedEmail',
                'creator'    => 'hal9000',
                'type'       => 'string',
                'value'      => 'johndoe@john.doe',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
        ];

        $features = $this->table('features');
        $features
            ->insert($data)
            ->save();
    }
}
