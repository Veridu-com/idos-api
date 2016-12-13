<?php
/*
 * Copyright (c) 20122016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class S12CategoriesSeed extends AbstractSeed {
    public function run() {
        $categories = [
            // Attributes
            [
                'display_name' => 'First name',
                'name'         => 'firstName',
                'type'         => 'attribute',
                'description'  => 'First name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last name',
                'name'         => 'lastName',
                'type'         => 'attribute',
                'description'  => 'Last name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Middle name',
                'name'         => 'middleName',
                'type'         => 'attribute',
                'description'  => 'Middle name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth day',
                'name'         => 'birthDay',
                'type'         => 'attribute',
                'description'  => 'Birth day of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth month',
                'name'         => 'birthMonth',
                'type'         => 'attribute',
                'description'  => 'Birth month of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth year',
                'name'         => 'birthYear',
                'type'         => 'attribute',
                'description'  => 'Birth year of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'City name',
                'name'         => 'cityName',
                'type'         => 'attribute',
                'description'  => 'City name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Country name',
                'name'         => 'countryName',
                'type'         => 'attribute',
                'description'  => 'Country name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Email',
                'name'         => 'email',
                'type'         => 'attribute',
                'description'  => 'Email of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Gender',
                'name'         => 'gender',
                'type'         => 'attribute',
                'description'  => 'Gender of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Phone',
                'name'         => 'phone',
                'type'         => 'attribute',
                'description'  => 'Phone of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Street address',
                'name'         => 'streetAddress',
                'type'         => 'attribute',
                'description'  => 'Street address of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Zipcode',
                'name'         => 'zipcode',
                'type'         => 'attribute',
                'description'  => 'Zipcode of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Profile',
                'name'         => 'profile',
                'type'         => 'attribute',
                'description'  => 'Profile attribute of a user.',
                'handler_id'   => 1
            ],

            // Flags
            [
                'display_name' => 'First name mismatch',
                'name'         => 'firstNameMismatch',
                'type'         => 'flag',
                'description'  => 'First name mismatch of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last name mismatch',
                'name'         => 'lastNameMismatch',
                'type'         => 'flag',
                'description'  => 'Last name mismatch of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Middle name mismatch',
                'name'         => 'middleNameMismatch',
                'type'         => 'flag',
                'description'  => 'Middle name mismatch of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Compromised email',
                'name'         => 'compromisedEmail',
                'type'         => 'flag',
                'description'  => 'Compromised email used in user account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Dropbox empty',
                'name'         => 'dropboxEmpty',
                'type'         => 'flag',
                'description'  => 'Empty dropbox account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Facebook empty',
                'name'         => 'facebookEmpty',
                'type'         => 'flag',
                'description'  => 'Empty facebook account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Google empty',
                'name'         => 'googleEmpty',
                'type'         => 'flag',
                'description'  => 'Empty google account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Linkedin empty',
                'name'         => 'linkedinEmpty',
                'type'         => 'flag',
                'description'  => 'Empty linkedin account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Spotify empty',
                'name'         => 'spotifyEmpty',
                'type'         => 'flag',
                'description'  => 'Empty spotify account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Twitter empty',
                'name'         => 'twitterEmpty',
                'type'         => 'flag',
                'description'  => 'Empty twitter account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Yahoo empty',
                'name'         => 'yahooEmpty',
                'type'         => 'flag',
                'description'  => 'Empty yahoo account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Empty account',
                'name'         => 'accountEmpty',
                'type'         => 'flag',
                'description'  => 'One of the submitted accounts is empty.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New account',
                'name'         => 'accountNew',
                'type'         => 'flag',
                'description'  => 'One of the submitted accounts is new.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New facebook',
                'name'         => 'facebookNew',
                'type'         => 'flag',
                'description'  => 'A new facebook account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New google',
                'name'         => 'googleNew',
                'type'         => 'flag',
                'description'  => 'A new google account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New paypal',
                'name'         => 'paypalNew',
                'type'         => 'flag',
                'description'  => 'A new paypal account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New twitter',
                'name'         => 'twitterNew',
                'type'         => 'flag',
                'description'  => 'A new twitter account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New yahoo',
                'name'         => 'yahooNew',
                'type'         => 'flag',
                'description'  => 'A new yahoo account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Recent name changes',
                'name'         => 'recentNameChanges',
                'type'         => 'flag',
                'description'  => 'Recent name changes.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Recent facebook name changes',
                'name'         => 'recentNameChangesFacebook',
                'type'         => 'flag',
                'description'  => 'Recent facebook name changes.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Recent google name changes',
                'name'         => 'recentNameChangesGoogle',
                'type'         => 'flag',
                'description'  => 'Recent google name changes.',
                'handler_id'   => 1
            ],

            // Scores
            [
                'display_name' => 'Birth day score',
                'name'         => 'birthDayScore',
                'type'         => 'score',
                'description'  => 'Birth day score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth month score',
                'name'         => 'birthMonthScore',
                'type'         => 'score',
                'description'  => 'Birth month score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth year score',
                'name'         => 'birthYearScore',
                'type'         => 'score',
                'description'  => 'Birth year score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'City name score',
                'name'         => 'cityNameScore',
                'type'         => 'score',
                'description'  => 'City name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Country name score',
                'name'         => 'countryNameScore',
                'type'         => 'score',
                'description'  => 'Country name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Email score',
                'name'         => 'emailScore',
                'type'         => 'score',
                'description'  => 'Email score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'First name score',
                'name'         => 'firstNameScore',
                'type'         => 'score',
                'description'  => 'First name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Gender score',
                'name'         => 'genderScore',
                'type'         => 'score',
                'description'  => 'Gender score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last name score',
                'name'         => 'lastNameScore',
                'type'         => 'score',
                'description'  => 'Last name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Phone score',
                'name'         => 'phoneScore',
                'type'         => 'score',
                'description'  => 'Phone score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Street address score',
                'name'         => 'streetAddressScore',
                'type'         => 'score',
                'description'  => 'Street address score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Zipcode score',
                'name'         => 'zipcodeScore',
                'type'         => 'score',
                'description'  => 'Zipcode score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'No Chargeback low score',
                'name'         => 'noChargebackScoreLow',
                'type'         => 'score',
                'description'  => 'No Chargeback low score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'No Chargeback medium score',
                'name'         => 'noChargebackScoreMed',
                'type'         => 'score',
                'description'  => 'No Chargeback medium score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'No Chargeback high score',
                'name'         => 'noChargebackScoreHigh',
                'type'         => 'score',
                'description'  => 'No Chargeback high score.',
                'handler_id'   => 1
            ],
            // Gates
            [
                'display_name' => 'No Chargeback',
                'name'         => 'noChargebackGate',
                'type'         => 'gate',
                'description'  => 'No Chargeback gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth day',
                'name'         => 'birthDayGate',
                'type'         => 'gate',
                'description'  => 'Birth day gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth month',
                'name'         => 'birthMonthGate',
                'type'         => 'gate',
                'description'  => 'Birth month gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth year',
                'name'         => 'birthYearGate',
                'type'         => 'gate',
                'description'  => 'Birth year gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'City name',
                'name'         => 'cityNameGate',
                'type'         => 'gate',
                'description'  => 'City name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Country name',
                'name'         => 'countryNameGate',
                'type'         => 'gate',
                'description'  => 'Country name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Email',
                'name'         => 'emailGate',
                'type'         => 'gate',
                'description'  => 'Email gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'First name',
                'name'         => 'firstNameGate',
                'type'         => 'gate',
                'description'  => 'First name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Gender',
                'name'         => 'genderGate',
                'type'         => 'gate',
                'description'  => 'Gender gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last Name',
                'name'         => 'lastNameGate',
                'type'         => 'gate',
                'description'  => 'Last Name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Phone',
                'name'         => 'phoneGate',
                'type'         => 'gate',
                'description'  => 'Phone gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Street address',
                'name'         => 'streetAddressGate',
                'type'         => 'gate',
                'description'  => 'Street address gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Zipcode',
                'name'         => 'zipcodeGate',
                'type'         => 'gate',
                'description'  => 'Zipcode gate.',
                'handler_id'   => 1
            ]
        ];

        $table = $this->table('categories');
        $table
            ->insert($categories)
            ->save();
    }
}
