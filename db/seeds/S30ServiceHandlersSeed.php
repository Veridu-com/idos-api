<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S30ServiceHandlersSeed extends AbstractSeed {
    public function run() {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'company_id'    => 1,
                'service_id'    => 1, // idOS Scraper
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 2, // idOS Feature Extractor
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 3, // idOS BirthYear Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 4, // idOS FirstName Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 5, // idOS LastName Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 6, // idOS BirthDay Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 7, // idOS BirthMonth Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 8, // idOS Country Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 9, // idOS E-mail Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 10, // idOS City Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 11, // idOS ZipCode Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 12, // idOS Phone Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 13, // idOS Street Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 14, // idOS Gender Candidates
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 15, // idOS FirstName Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 16, // idOS Overall Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 17, // idOS BirthYear Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 18, // idOS LastName Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 19, // idOS BirthDay Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 20, // idOS BirthMonth Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 21, // idOS Country Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 22, // idOS E-mail Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 23, // idOS Overall Model C
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 24, // idOS City Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 25, // idOS ZipCode Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 26, // idOS Phone Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 27, // idOS Street Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 28, // idOS Gender Model M
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
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 29, // idOS E-mail handler - Invitation e-mail
                'listens'       => json_encode(
                    [
                        'idos:invitation.created',
                        'idos:invitation.resend'
                    ]
                ),
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 30, // idOS OTP Email handler
                'listens'       => json_encode(
                    [
                        'idos:otp.email.created',
                    ]
                ),
                'created_at' => $now
            ]
        ];

        $service_handlers = $this->table('service_handlers');
        $service_handlers
            ->insert($data)
            ->save();
    }
}
