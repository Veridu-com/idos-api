<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S11HandlerServicesSeed extends AbstractSeed {
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
                'idos:feature.yahoo.created',
                'idos:feature.tracesmart.created'
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
                'idos:raw.yahoo.created',
                'idos:raw.tracesmart.created'
            ]
        );

        $handlerServiceData = [
            [
                'name'          => 'idOS Scraper',
                'url'           => 'https://scrape.idos.io/1.0/scrape',
                'handler_id'    => 2,
                'listens'       => $sourceListens,
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS Feature Extractor',
                'url'           => 'https://feature.idos.io/1.0/feature',
                'handler_id'    => 3,
                'listens'       => $rawListens,
                'enabled'       => true,
            ],
            [
                'name'          => 'idOS BirthYear Candidates',
                'url'           => 'https://model.idos.io/morpheus/birthyear-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS FirstName Candidates',
                'url'           => 'https://model.idos.io/morpheus/firstname-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS LastName Candidates',
                'url'           => 'https://model.idos.io/morpheus/lastname-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthDay Candidates',
                'url'           => 'https://model.idos.io/morpheus/birthday-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthMonth Candidates',
                'url'           => 'https://model.idos.io/morpheus/birthmonth-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Country Candidates',
                'url'           => 'https://model.idos.io/morpheus/country-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS E-mail Candidates',
                'url'           => 'https://model.idos.io/morpheus/email-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS City Candidates',
                'url'           => 'https://model.idos.io/morpheus/city-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS ZipCode Candidates',
                'url'           => 'https://model.idos.io/morpheus/zipcode-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Phone Candidates',
                'url'           => 'https://model.idos.io/morpheus/phone-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Street Candidates',
                'url'           => 'https://model.idos.io/morpheus/street-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Gender Candidates',
                'url'           => 'https://model.idos.io/morpheus/gender-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS FirstName',
                'url'           => 'https://model.idos.io/morpheus/firstname-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthYear',
                'url'           => 'https://model.idos.io/morpheus/birthyear-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS LastName',
                'url'           => 'https://model.idos.io/morpheus/lastname-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthDay',
                'url'           => 'https://model.idos.io/morpheus/birthday-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS BirthMonth',
                'url'           => 'https://model.idos.io/morpheus/birthmonth-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Country',
                'url'           => 'https://model.idos.io/morpheus/country-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS E-mail',
                'url'           => 'https://model.idos.io/morpheus/email-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS No Chargeback',
                'url'           => 'https://model.idos.io/morpheus/overall-cs-nb',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS City',
                'url'           => 'https://model.idos.io/morpheus/city-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS ZipCode',
                'url'           => 'https://model.idos.io/morpheus/zipcode-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Phone',
                'url'           => 'https://model.idos.io/morpheus/phone-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Street',
                'url'           => 'https://model.idos.io/morpheus/street-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Gender',
                'url'           => 'https://model.idos.io/morpheus/gender-mlp',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS E-mail Handler',
                'url'           => 'https://email.idos.io/1.0/email/invitation',
                'handler_id'    => 4,
                'listens'       => json_encode(
                    [
                        'idos:invitation.created',
                        'idos:invitation.resend'
                    ]
                ),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS OTP E-mail Handler',
                'url'           => 'https://email.idos.io/1.0/email/otp',
                'handler_id'    => 4,
                'listens'       => json_encode(
                    [
                        'idos:otp.email.created'
                    ]
                ),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS OTP SMS Handler',
                'url'           => 'https://sms.idos.io/1.0/sms/otp',
                'handler_id'    => 5,
                'listens'       => json_encode(
                    [
                        'idos:otp.phone.created'
                    ]
                ),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS CRA Handler for TraceSmart',
                'url'           => 'https://cra.idos.io/1.0/cra/tracesmart',
                'handler_id'    => 6,
                'listens'       => json_encode(
                    [
                        'idos:cra.tracesmart'
                    ]
                ),
                'enabled'  => true,
            ],
            [
                'name'          => 'idOS ProfilePicture Candidates',
                'url'           => 'https://model.idos.io/morpheus/profilepic-candidates',
                'handler_id'    => 1,
                'listens'       => $featureListens,
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Widget Handler',
                'handler_id'    => 7,
                'url'           => 'https://widget.idos.io/1.0',
                'listens'       => json_encode([]),
                'enabled'       => true
            ],
            [
                'name'          => 'idOS Recommendation',
                'url'           => 'https://model.idos.io/morpheus/recommendation',
                'handler_id'    => 3,
                'listens'       => json_encode(['idos.recommendation']),
                'enabled'       => true
            ]
        ];

        $table = $this->table('handler_services');
        $table
            ->insert($handlerServiceData)
            ->save();
    }
}
