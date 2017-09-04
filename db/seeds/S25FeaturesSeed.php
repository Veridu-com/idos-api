<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

class S25FeaturesSeed extends Db\AbstractExtendedSeed {
    public function run() {
        $data = [
            [
                'user_id'    => 1,
                'source'     => 'facebook',
                'name'       => 'birthYear',
                'creator'    => 3,
                'type'       => 'integer',
                'value'      => $this->lock('1985'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source'     => 'facebook',
                'name'       => 'birthMonth',
                'creator'    => 3,
                'type'       => 'integer',
                'value'      => $this->lock('10'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source'     => 'facebook',
                'name'       => 'birthDay',
                'creator'    => 3,
                'type'       => 'integer',
                'value'      => $this->lock('13'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source'     => 'facebook',
                'name'       => 'numOfFriends',
                'creator'    => 3,
                'type'       => 'integer',
                'value'      => $this->lock('4'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source'     => 'facebook',
                'name'       => 'isVerified',
                'creator'    => 3,
                'type'       => 'boolean',
                'value'      => $this->lock('false'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'source'     => 'linkedin',
                'name'       => 'isCelebrity',
                'creator'    => 3,
                'type'       => 'boolean',
                'value'      => $this->lock(''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 1,
                'source'     => null,
                'name'       => 'submittedName',
                'creator'    => 1,
                'type'       => 'string',
                'value'      => $this->lock('John Doe'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'user_id'    => 2,
                'source'     => null,
                'name'       => 'submittedEmail',
                'creator'    => 1,
                'type'       => 'string',
                'value'      => $this->lock('johndoe@john.doe'),
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
