<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S11ServicesSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $servicesData = [
            [
                'name'          => 'idOS Scraper',
                'url'           => 'https://handler.idos.io/1.0/scrape',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-1'), // ef970ffad1f1253a2182a88667233991
                'private'       => md5('private-1'), // 213b83392b80ee98c8eb2a9fed9bb84d
                'listens'       => json_encode(
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
                ),
                'triggers'      => json_encode(['handler:scrape.completed']),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Feature Extractor',
                'url'           => 'https://handler.idos.io/1.0/feature',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-2'), // 8c178e650645a1f2a0c7de98757373b6
                'private'       => md5('private-2'), // e603de4692c2179446a96374bce86ce6
                'listens'       => json_encode(
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
                ),
                'triggers'      => json_encode(['handler:feature.completed']),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS BirthYear Candidates',
                'url'           => 'https://handler.idos.io/morpheus/birthyear-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-3'), // 043578887a8013e3805a789927b0fbf2
                'private'       => md5('private-3'), // 36bf101e92f80f4033b588e6ce4a746b
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:birthyear-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS FirstName Candidates',
                'url'           => 'https://handler.idos.io/morpheus/firstname-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-4'), // d9350e4efaa82ab03f6a116e7927887c
                'private'       => md5('private-4'), // 790747e70231480a9d3773e11c9e6e33
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:firstname-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS LastName Candidates',
                'url'           => 'https://handler.idos.io/morpheus/lastname-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-5'), // 34e2ce59bd311108100d5b8488707e7d
                'private'       => md5('private-5'), // a47ff6cda37a055159e51619da4c1eda
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:lastname-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS BirthDay Candidates',
                'url'           => 'https://handler.idos.io/morpheus/birthday-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-6'), // 822cb4e2d3da95a036d44d858c54f8ce
                'private'       => md5('private-6'), // 163a5ee399770960099fe8401e647799
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:birthday-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS BirthMonth Candidates',
                'url'           => 'https://handler.idos.io/morpheus/birthmonth-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-7'), // 4dcbe1eb3441fe15eef01b89d7abdfa4
                'private'       => md5('private-7'), // ae275ef6b16e393532dcf6d9cc97a466
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:birthmonth-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Country Candidates',
                'url'           => 'https://handler.idos.io/morpheus/country-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-8'), // 7fee8850d88bba49a332eff6e645dbad
                'private'       => md5('private-8'), // 1d504f98b2ec02778df1c1e74cfbf59d
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:country-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS E-mail Candidates',
                'url'           => 'https://handler.idos.io/morpheus/email-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-9'), // c593cbd73c289a733d09063ad26f98be
                'private'       => md5('private-9'), // eff2258d7989e11ead93cb5e7fd000dc
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:email-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS City Candidates',
                'url'           => 'https://handler.idos.io/morpheus/city-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-10'), // 892d012b1979c55d61ae007a741cc30b
                'private'       => md5('private-10'), // 878f6cfacf67067cdc908eb928605422
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:city-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS ZipCode Candidates',
                'url'           => 'https://handler.idos.io/morpheus/zipcode-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-11'), // c0c18a641057586ee6d002f683160e52
                'private'       => md5('private-11'), // 22472b387999651e1a3e9d6749d5b5be
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:zipcode-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Phone Candidates',
                'url'           => 'https://handler.idos.io/morpheus/phone-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-12'), // e35ab4ec43d55657bcefb653b2380b8a
                'private'       => md5('private-12'), // 8622559ad4dd71c404a006ef8741bb0a
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:phone-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Street Candidates',
                'url'           => 'https://handler.idos.io/morpheus/street-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-13'), // b5ff2dafb6a5d9e9c33e0ebb3328113d
                'private'       => md5('private-13'), // 2e7e7d0c800ed7ed78bc1210285a2c22
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:street-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Gender Candidates',
                'url'           => 'https://handler.idos.io/morpheus/gender-candidates',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-14'), // 55094d5eb6130bd4d9f04dca1b512032
                'private'       => md5('private-14'), // 80027827316593f525f3f5836c153b97
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:gender-candidates.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS FirstName Model M',
                'url'           => 'https://handler.idos.io/morpheus/firstname-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-15'), // ef17eb6241e0164359f8ca7209a26e09
                'private'       => md5('private-15'), // f6228187f3c051b569596bfc1485139c
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:firstname-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Overall Model M',
                'url'           => 'https://handler.idos.io/morpheus/overall-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-16'), // 22b41cbe535bdf392ea5229902f81078
                'private'       => md5('private-16'), // 61d0a0a7d5a19e66de5f1c8b586c27dd
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:overall-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS BirthYear Model M',
                'url'           => 'https://handler.idos.io/morpheus/birthyear-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-17'), // 5bfccffe6e102ce340700283ff936d4e
                'private'       => md5('private-17'), // bc2f2ce3e2f645529b5b363bc6590b56
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:birthyear-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS LastName Model M',
                'url'           => 'https://handler.idos.io/morpheus/lastname-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-18'), // 6080b3b7122428ee34c75bdd2f9bf3ac
                'private'       => md5('private-18'), // 40f3d480d983c1b7d2f6acc006fa0329
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:lastname-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS BirthDay Model M',
                'url'           => 'https://handler.idos.io/morpheus/birthday-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-19'), // b7d3aecd61e5e301a9e1a211ab6ec1ee
                'private'       => md5('private-19'), // aadae6f98f3b8069ccca9870cdc76b41
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:birthday-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS BirthMonth Model M',
                'url'           => 'https://handler.idos.io/morpheus/birthmonth-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-20'), // 054bae08c75120d1744fc27aaef78fc8
                'private'       => md5('private-20'), // 9907933ad2631dcdf29208e7b4b37e08
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:birthmonth-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Country Model M',
                'url'           => 'https://handler.idos.io/morpheus/country-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-21'), // 7de41cec4adffb88b4ef6c5280afbb28
                'private'       => md5('private-21'), // 4e3fa277e469fdc9b280d5c83fc96494
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:country-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS E-mail Model M',
                'url'           => 'https://handler.idos.io/morpheus/email-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-22'), // 829f85c9649332ddac6a2fc5e3e23c5b
                'private'       => md5('private-22'), // 9d5694285e87f31096490e73546ffd2a
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:email-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Overall Model C',
                'url'           => 'https://handler.idos.io/morpheus/overall-cs-nb',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-23'), // 3e3650ffd72619046a7adb0190cd21c4
                'private'       => md5('private-23'), // d0fb0bc7ec6331c4d87c0bee39c9dcb3
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:overall-cs-nb.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS City Model M',
                'url'           => 'https://handler.idos.io/morpheus/city-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-24'), // 452a0d5314c96a72800a7708d872ce73
                'private'       => md5('private-24'), // 3b7640b56a87ca3381a1589f69f5ff5a
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:city-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS ZipCode Model M',
                'url'           => 'https://handler.idos.io/morpheus/zipcode-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-25'), // 8e156040a1756307b7be3777d3cbb6ab
                'private'       => md5('private-25'), // 231a40b3b78c43ff281cf661b08b1e9d
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:zipcode-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Phone Model M',
                'url'           => 'https://handler.idos.io/morpheus/phone-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-26'), // fd9780e9a1f092cebf3a415673ed1d4e
                'private'       => md5('private-26'), // f75895e0ac0d19e363013ec833890ff7
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:phone-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Street Model M',
                'url'           => 'https://handler.idos.io/morpheus/street-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-27'), // 945637ce1c7f1a4b00ff6ffe20893cef
                'private'       => md5('private-27'), // d500ac82c0ac5ea787970685ab687ba2
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:street-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS Gender Model M',
                'url'           => 'https://handler.idos.io/morpheus/gender-mlp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-28'), // 79843523db30983abd8c6e6b1d43aad3
                'private'       => md5('private-28'), // 68b680ec10c8e2be4c9c10eb58e1278a
                'listens'       => json_encode(
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
                ),
                'triggers' => json_encode(['handler:gender-mlp.completed']),
                'enabled'  => true
            ],
            [
                'name'          => 'idOS E-mail Handler',
                'url'           => 'https://handler.idos.io/1.0/email/invitation',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-29'), // aafc17b2c826b02b9bec9de6e37d5ea9
                'private'       => md5('private-29'), // d9288a19a2abe8351e12ce90bd761c42
                'listens'       => json_encode(
                    [
                        'idos:invitation.created',
                        'idos:invitation.resend'
                    ]
                ),
                'triggers'      => json_encode([]),
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS OTP E-mail Handler',
                'url'           => 'https://handler.idos.io/1.0/email/otp',
                'company_id'    => 1,
                'auth_username' => '***REMOVED***',
                'auth_password' => '***REMOVED***',
                'public'        => md5('public-30'), // aafc17b2c826b02b9bec9de6e37d5ea9
                'private'       => md5('private-30'), // d9288a19a2abe8351e12ce90bd761c42
                'listens'       => json_encode(
                    [
                        'idos:otp.email.created'
                    ]
                ),
                'triggers'      => json_encode([]),
                'enabled'       => true,
            ]
        ];

        $table = $this->table('services');
        $table
            ->insert($servicesData)
            ->save();

    }
}
