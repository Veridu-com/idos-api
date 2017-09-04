<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

class S10HandlersSeed extends Db\AbstractExtendedSeed {
    public function run() {
        $handlers = [
            [
                'name'          => 'idOS Machine Learning',
                'role'          => 'machine-learning',
                'company_id'    => 1,
                'auth_username' => $this->lock('***REMOVED***'),
                'auth_password' => $this->lock('***REMOVED***'),
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => $this->lock('81197557e9117dfd6f16cb72a2710830'),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Scraper',
                'company_id'    => 1,
                'role'          => 'data-gathering',
                'auth_username' => $this->lock('***REMOVED***'),
                'auth_password' => $this->lock('***REMOVED***'),
                'public'        => 'eab842a8a9369fe89859427e8350fccd',
                'private'       => $this->lock('1ab44fa6767904d426d9848514c93be5'),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Feature Extractor',
                'company_id'    => 1,
                'role'          => 'feature-extractor',
                'auth_username' => $this->lock('***REMOVED***'),
                'auth_password' => $this->lock('***REMOVED***'),
                'public'        => '36901fee71b4d7045c024e32aae62cb8',
                'private'       => $this->lock('bded344cbef1ea5ff52a2abb52c89e92'),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Email',
                'company_id'    => 1,
                'role'          => 'email-sender',
                'auth_username' => $this->lock('***REMOVED***'),
                'auth_password' => $this->lock('***REMOVED***'),
                'public'        => '0d8e6b0687264c91d649c00e806194ca',
                'private'       => $this->lock('44f31cf16ea60ad459250221968837b0'),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Widget',
                'company_id'    => 1,
                'role'          => 'standalone',
                'auth_username' => $this->lock('***REMOVED***'),
                'auth_password' => $this->lock('***REMOVED***'),
                'public'        => 'fc67686377379aa3e61220b663630759',
                'private'       => $this->lock('75ccf03ad65d10878a4efa73a228c239'),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS SMS',
                'company_id'    => 1,
                'role'          => 'sms-sender',
                'auth_username' => $this->lock('***REMOVED***'),
                'auth_password' => $this->lock('***REMOVED***'),
                'public'        => 'e1300d249069a3f4caad47c3be5cb589',
                'private'       => $this->lock('cd4e4ae062ca7177fb1380cf3dd23c27'),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS CRA',
                'company_id'    => 1,
                'role'          => 'cra-sender',
                'auth_username' => $this->lock('***REMOVED***'),
                'auth_password' => $this->lock('***REMOVED***'),
                'public'        => '07e39b8686a34adef8ec913e074d6274',
                'private'       => $this->lock('e18db301f4a6d99985f8ced05fa41ae7'),
                'enabled'       => true,
            ]
        ];

        $table = $this->table('handlers');
        $table
            ->insert($handlers)
            ->save();
    }
}
