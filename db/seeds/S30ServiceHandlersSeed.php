<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S30ServiceHandlersSeed extends AbstractSeed {
    public function run() {
        $now            = date('Y-m-d H:i:s');
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
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 4, // idOS FirstName Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 5, // idOS LastName Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 6, // idOS BirthDay Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 7, // idOS BirthMonth Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 8, // idOS Country Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 9, // idOS E-mail Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 10, // idOS City Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 11, // idOS ZipCode Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 12, // idOS Phone Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 13, // idOS Street Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 14, // idOS Gender Candidates
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 15, // idOS FirstName
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 16, // idOS BirthYear
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 17, // idOS LastName
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 18, // idOS BirthDay
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 19, // idOS BirthMonth
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 20, // idOS Country
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 21, // idOS E-mail
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 22, // idOS No Chargeback
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 23, // idOS City
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 24, // idOS ZipCode
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 25, // idOS Phone
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 26, // idOS Street
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 27, // idOS Gender
                'listens'       => $featureListens,
                'created_at'    => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 28, // idOS E-mail handler - Invitation e-mail
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
                'service_id'    => 29, // idOS OTP Email handler
                'listens'       => json_encode(
                    [
                        'idos:otp.email.created',
                    ]
                ),
                'created_at' => $now
            ],
            [
                'company_id'    => 1,
                'service_id'    => 30, // idOS ProfilePicture Candidates
                'listens'       => $featureListens,
                'created_at' => $now
            ]
        ];

        $service_handlers = $this->table('service_handlers');
        $service_handlers
            ->insert($data)
            ->save();
    }
}

/**
 * Created gates:.
 *
 * id |            name
 * ----+----------------------------
 * 1 | idOS Scraper
 * 2 | idOS Feature Extractor
 * 3 | idOS BirthYear Candidates
 * 4 | idOS FirstName Candidates
 * 5 | idOS LastName Candidates
 * 6 | idOS BirthDay Candidates
 * 7 | idOS BirthMonth Candidates
 * 8 | idOS Country Candidates
 * 9 | idOS E-mail Candidates
 * 10 | idOS City Candidates
 * 11 | idOS ZipCode Candidates
 * 12 | idOS Phone Candidates
 * 13 | idOS Street Candidates
 * 14 | idOS Gender Candidates
 * 15 | idOS FirstName
 * 16 | idOS BirthYear
 * 17 | idOS LastName
 * 18 | idOS BirthDay
 * 19 | idOS BirthMonth
 * 20 | idOS Country
 * 21 | idOS E-mail
 * 22 | idOS No Chargeback
 * 23 | idOS City
 * 24 | idOS ZipCode
 * 25 | idOS Phone
 * 26 | idOS Street
 * 27 | idOS Gender
 * 28 | idOS E-mail Handler
 */
