<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S12CategoriesSeed extends AbstractSeed {
    public function run() {

        $categories = [
            // Attributes
            [
                'name'        => 'First name',
                'slug'        => 'first-name',
                'type'        => 'attribute',
                'description' => 'First name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Last name',
                'slug'        => 'last-name',
                'type'        => 'attribute',
                'description' => 'Last name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Middle name',
                'slug'        => 'middle-name',
                'type'        => 'attribute',
                'description' => 'Middle name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth day',
                'slug'        => 'birth-day',
                'type'        => 'attribute',
                'description' => 'Birth day of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth month',
                'slug'        => 'birth-month',
                'type'        => 'attribute',
                'description' => 'Birth month of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth year',
                'slug'        => 'birth-year',
                'type'        => 'attribute',
                'description' => 'Birth year of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'City name',
                'slug'        => 'city-name',
                'type'        => 'attribute',
                'description' => 'City name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Country name',
                'slug'        => 'country-name',
                'type'        => 'attribute',
                'description' => 'Country name of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Email',
                'slug'        => 'email',
                'type'        => 'attribute',
                'description' => 'Email of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Gender',
                'slug'        => 'gender',
                'type'        => 'attribute',
                'description' => 'Gender of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Phone',
                'slug'        => 'phone',
                'type'        => 'attribute',
                'description' => 'Phone of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Street address',
                'slug'        => 'street-address',
                'type'        => 'attribute',
                'description' => 'Street address of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Zipcode',
                'slug'        => 'zipcode',
                'type'        => 'attribute',
                'description' => 'Zipcode of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Profile',
                'slug'        => 'profile',
                'type'        => 'attribute',
                'description' => 'Profile attribute of a user.',
                'service_id'  => 1
            ],

            // Flags
            [
                'name'        => 'First name mismatch',
                'slug'        => 'first-name-mismatch',
                'type'        => 'flag',
                'description' => 'First name mismatch of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Last name mismatch',
                'slug'        => 'last-name-mismatch',
                'type'        => 'flag',
                'description' => 'Last name mismatch of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Middle name mismatch',
                'slug'        => 'middle-name-mismatch',
                'type'        => 'flag',
                'description' => 'Middle name mismatch of a user.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Compromised email',
                'slug'        => 'compromised-email',
                'type'        => 'flag',
                'description' => 'Compromised email used in user account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Dropbox empty',
                'slug'        => 'dropbox-empty',
                'type'        => 'flag',
                'description' => 'Empty dropbox account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Facebook empty',
                'slug'        => 'facebook-empty',
                'type'        => 'flag',
                'description' => 'Empty facebook account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Google empty',
                'slug'        => 'google-empty',
                'type'        => 'flag',
                'description' => 'Empty google account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Linkedin empty',
                'slug'        => 'linkedin-empty',
                'type'        => 'flag',
                'description' => 'Empty linkedin account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Spotify empty',
                'slug'        => 'spotify-empty',
                'type'        => 'flag',
                'description' => 'Empty spotify account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Twitter empty',
                'slug'        => 'twitter-empty',
                'type'        => 'flag',
                'description' => 'Empty twitter account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Yahoo empty',
                'slug'        => 'yahoo-empty',
                'type'        => 'flag',
                'description' => 'Empty yahoo account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Empty account',
                'slug'        => 'account-empty',
                'type'        => 'flag',
                'description' => 'One of the submitted accounts is empty.',
                'service_id'  => 1
            ],
            [
                'name'        => 'New account',
                'slug'        => 'account-new',
                'type'        => 'flag',
                'description' => 'One of the submitted accounts is new.',
                'service_id'  => 1
            ],
            [
                'name'        => 'New facebook',
                'slug'        => 'facebook-new',
                'type'        => 'flag',
                'description' => 'A new facebook account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'New google',
                'slug'        => 'google-new',
                'type'        => 'flag',
                'description' => 'A new google account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'New paypal',
                'slug'        => 'paypal-new',
                'type'        => 'flag',
                'description' => 'A new paypal account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'New twitter',
                'slug'        => 'twitter-new',
                'type'        => 'flag',
                'description' => 'A new twitter account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'New yahoo',
                'slug'        => 'yahoo-new',
                'type'        => 'flag',
                'description' => 'A new yahoo account.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Recent name changes',
                'slug'        => 'recent-name-changes',
                'type'        => 'flag',
                'description' => 'Recent name changes.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Recent facebook name changes',
                'slug'        => 'recent-name-changes-facebook',
                'type'        => 'flag',
                'description' => 'Recent facebook name changes.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Recent google name changes',
                'slug'        => 'recent-name-changes-google',
                'type'        => 'flag',
                'description' => 'Recent google name changes.',
                'service_id'  => 1
            ],

            // Scores
            [
                'name'        => 'Overall',
                'slug'        => 'overall-score-series-s-model-csnb',
                'type'        => 'score',
                'description' => 'Overall profile score for model series S.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth day score',
                'slug'        => 'birth-day-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Birth day score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth month score',
                'slug'        => 'birth-month-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Birth month score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth year score',
                'slug'        => 'birth-year-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Birth year score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'City name score',
                'slug'        => 'city-name-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'City name score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Country name score',
                'slug'        => 'country-name-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Country name score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Email score',
                'slug'        => 'email-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Email score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'First name score',
                'slug'        => 'first-name-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'First name score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Gender score',
                'slug'        => 'gender-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Gender score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Last name score',
                'slug'        => 'last-name-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Last name score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Overall score',
                'slug'        => 'overall-score-series-m-model-m',
                'type'        => 'score',
                'description' => 'Overall profile score for model series M.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Phone score',
                'slug'        => 'phone-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Phone score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Street address score',
                'slug'        => 'street-address-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Street address score.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Zipcode score',
                'slug'        => 'zipcode-score-series-s-model-m',
                'type'        => 'score',
                'description' => 'Zipcode score.',
                'service_id'  => 1
            ],

            // Gates
            [
                'name'        => 'Chargeback',
                'slug'        => 'chargeback',
                'type'        => 'gate',
                'description' => 'Chargeback gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth day gate',
                'slug'        => 'birth-day-gate',
                'type'        => 'gate',
                'description' => 'Birth day gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth month gate',
                'slug'        => 'birth-month-gate',
                'type'        => 'gate',
                'description' => 'Birth month gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Birth year gate',
                'slug'        => 'birth-year-gate',
                'type'        => 'gate',
                'description' => 'Birth year gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'City name gate',
                'slug'        => 'city-name-gate',
                'type'        => 'gate',
                'description' => 'City name gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Country name gate',
                'slug'        => 'country-name-gate',
                'type'        => 'gate',
                'description' => 'Country name gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Email gate',
                'slug'        => 'email-gate',
                'type'        => 'gate',
                'description' => 'Email gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'First name gate',
                'slug'        => 'first-name-gate',
                'type'        => 'gate',
                'description' => 'First name gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Gender gate',
                'slug'        => 'gender-gate',
                'type'        => 'gate',
                'description' => 'Gender gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Last Name gate',
                'slug'        => 'last-name-gate',
                'type'        => 'gate',
                'description' => 'Last Name gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Phone gate',
                'slug'        => 'phone-gate',
                'type'        => 'gate',
                'description' => 'Phone gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Street address gate',
                'slug'        => 'street-address-gate',
                'type'        => 'gate',
                'description' => 'Street address gate.',
                'service_id'  => 1
            ],
            [
                'name'        => 'Zipcode gate',
                'slug'        => 'zipcode-gate',
                'type'        => 'gate',
                'description' => 'Zipcode gate.',
                'service_id'  => 1
            ]

        ];

        $features = $table = $this->table('categories');
        $table
            ->insert($categories)
            ->save();
    }
}
