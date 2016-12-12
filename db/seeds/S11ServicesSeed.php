<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S11ServicesSeed extends AbstractSeed {
    public function run() {
        $sourceListens = json_encode(
            [
                'idos:source.amazon.created',
                'idos:source.dropbox.created',
                'idos:source.facebook.created',
                'idos:source.google.created',
                'idos:source.linkedin.created',
                'idos:source.paypal.created',
                'idos:source.spotify.created',
                'idos:source.twitter.created',
                'idos:source.yahoo.created'
            ]
        );

        $featureListens = json_encode(
            [
                'idos:feature.amazon.created',
                'idos:feature.dropbox.created',
                'idos:feature.facebook.created',
                'idos:feature.google.created',
                'idos:feature.linkedin.created',
                'idos:feature.paypal.created',
                'idos:feature.spotify.created',
                'idos:feature.twitter.created',
                'idos:feature.yahoo.created'
            ]
        );

        $rawListens = json_encode(
            [
                'idos:raw.amazon.created',
                'idos:raw.dropbox.created',
                'idos:raw.facebook.created',
                'idos:raw.google.created',
                'idos:raw.linkedin.created',
                'idos:raw.paypal.created',
                'idos:raw.spotify.created',
                'idos:raw.twitter.created',
                'idos:raw.yahoo.created'
            ]
        );

        $servicesData = [
            [
                'name'          => 'idOS Scraper',
                'url'           => 'https://scrape.idos.io/1.0/scrape',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'eab842a8a9369fe89859427e8350fccd',
                'private'       => '1ab44fa6767904d426d9848514c93be5',
                'listens'       => $sourceListens,
                'triggers' => json_encode(['handler:scrape.completed']),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS Feature Extractor',
                'url'           => 'https://feature.idos.io/1.0/feature',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => '36901fee71b4d7045c024e32aae62cb8',
                'private'       => 'bded344cbef1ea5ff52a2abb52c89e92',
                'listens'       => $rawListens,
                'triggers' => json_encode(['handler:feature.completed']),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS BirthYear Candidates',
                'url'           => 'https://model.idos.io/morpheus/birthyear-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:birthyear-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS FirstName Candidates',
                'url'           => 'https://model.idos.io/morpheus/firstname-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:firstname-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS LastName Candidates',
                'url'           => 'https://model.idos.io/morpheus/lastname-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:lastname-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthDay Candidates',
                'url'           => 'https://model.idos.io/morpheus/birthday-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:birthday-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthMonth Candidates',
                'url'           => 'https://model.idos.io/morpheus/birthmonth-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:birthmonth-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Country Candidates',
                'url'           => 'https://model.idos.io/morpheus/country-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:country-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS E-mail Candidates',
                'url'           => 'https://model.idos.io/morpheus/email-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:email-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS City Candidates',
                'url'           => 'https://model.idos.io/morpheus/city-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:city-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS ZipCode Candidates',
                'url'           => 'https://model.idos.io/morpheus/zipcode-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:zipcode-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Phone Candidates',
                'url'           => 'https://model.idos.io/morpheus/phone-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:phone-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Street Candidates',
                'url'           => 'https://model.idos.io/morpheus/street-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:street-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Gender Candidates',
                'url'           => 'https://model.idos.io/morpheus/gender-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:gender-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS FirstName',
                'url'           => 'https://model.idos.io/morpheus/firstname-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:firstname-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthYear',
                'url'           => 'https://model.idos.io/morpheus/birthyear-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:birthyear-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS LastName',
                'url'           => 'https://model.idos.io/morpheus/lastname-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:lastname-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthDay',
                'url'           => 'https://model.idos.io/morpheus/birthday-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:birthday-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthMonth',
                'url'           => 'https://model.idos.io/morpheus/birthmonth-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:birthmonth-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Country',
                'url'           => 'https://model.idos.io/morpheus/country-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:country-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS E-mail',
                'url'           => 'https://model.idos.io/morpheus/email-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:email-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS No Chargeback',
                'url'           => 'https://model.idos.io/morpheus/overall-cs-nb',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:overall-cs-nb.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS City',
                'url'           => 'https://model.idos.io/morpheus/city-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:city-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS ZipCode',
                'url'           => 'https://model.idos.io/morpheus/zipcode-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:zipcode-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Phone',
                'url'           => 'https://model.idos.io/morpheus/phone-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:phone-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Street',
                'url'           => 'https://model.idos.io/morpheus/street-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:street-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Gender',
                'url'           => 'https://model.idos.io/morpheus/gender-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:gender-mlp.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS E-mail Handler',
                'url'           => 'https://email.idos.io/1.0/email/invitation',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => '0d8e6b0687264c91d649c00e806194ca',
                'private'       => '44f31cf16ea60ad459250221968837b0',
                'listens'       => json_encode(
                    [
                        'idos:invitation.created',
                        'idos:invitation.resend'
                    ]
                ),
                'triggers' => json_encode([]),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS OTP E-mail Handler',
                'url'           => 'https://email.idos.io/1.0/email/otp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => '0d8e6b0687264c91d649c00e806194ca',
                'private'       => '44f31cf16ea60ad459250221968837b0',
                'listens'       => json_encode(
                    [
                        'idos:otp.email.created'
                    ]
                ),
                'triggers' => json_encode([]),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS ProfilePicture Candidates',
                'url'           => 'https://model.idos.io/morpheus/profilepic-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => $featureListens,
                'triggers'      => json_encode(['handler:profilepicture-candidates.completed']),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Widget Handler',
                'url'           => 'https://widget.idos.io/1.0',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'fc67686377379aa3e61220b663630759',
                'private'       => '75ccf03ad65d10878a4efa73a228c239',
                'listens'       => json_encode([]),
                'triggers'      => json_encode([]),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Recommendation',
                'url'           => 'https://model.idos.io/morpheus/recommendation',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => 'b16c931c061e14af275bd2c86d3cf48d',
                'private'       => '81197557e9117dfd6f16cb72a2710830',
                'listens'       => json_encode(['idos.recommendation']),
                'triggers'      => json_encode([]),
                'enabled'       => true
            ]
        ];

        $table = $this->table('services');
        $table
            ->insert($servicesData)
            ->save();
    }
}
