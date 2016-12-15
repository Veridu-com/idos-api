<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S10HandlersSeed extends AbstractSeed {
    public function run() {
        $handlers = [
            [
                'name' => 'idOS Machine Learning',
                'role' => 'machine-learning',
                'company_id' => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public' => 'b16c931c061e14af275bd2c86d3cf48d',
                'private' => '81197557e9117dfd6f16cb72a2710830',
                'enabled' => true,
            ],
            [
                'name' => 'idOS Scraper',
                'company_id' => 1,
                'role' => 'data-gathering',
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public' => 'eab842a8a9369fe89859427e8350fccd',
                'private' => '1ab44fa6767904d426d9848514c93be5',
                'enabled' => true,
            ],
            [
                'name' => 'idOS Feature Extractor',
                'company_id' => 1,
                'role' => 'feature-extractor',
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public' => '36901fee71b4d7045c024e32aae62cb8',
                'private' => 'bded344cbef1ea5ff52a2abb52c89e92',
                'enabled' => true,
            ],
            [
                'name' => 'idOS Email',
                'company_id' => 1,
                'role' => 'email-sender',
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public' => '0d8e6b0687264c91d649c00e806194ca',
                'private' => '44f31cf16ea60ad459250221968837b0',
                'enabled' => true,
            ],
            [
                'name' => 'idOS Widget',
                'company_id' => 1,
                'role' => 'standalone',
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public' => 'fc67686377379aa3e61220b663630759',
                'private' => '75ccf03ad65d10878a4efa73a228c239',
                'enabled' => true,
            ],
        ];

        $table = $this->table('handlers');
        $table
            ->insert($handlers)
            ->save();
    }
}
