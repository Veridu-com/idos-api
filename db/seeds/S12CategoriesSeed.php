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
                'slug'         => 'first-name',
                'type'         => 'attribute',
                'description'  => 'First name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last name',
                'name'         => 'lastName',
                'slug'         => 'last-name',
                'type'         => 'attribute',
                'description'  => 'Last name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Middle name',
                'name'         => 'middleName',
                'slug'         => 'middle-name',
                'type'         => 'attribute',
                'description'  => 'Middle name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth day',
                'name'         => 'birthDay',
                'slug'         => 'birth-day',
                'type'         => 'attribute',
                'description'  => 'Birth day of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth month',
                'name'         => 'birthMonth',
                'slug'         => 'birth-month',
                'type'         => 'attribute',
                'description'  => 'Birth month of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth year',
                'name'         => 'birthYear',
                'slug'         => 'birth-year',
                'type'         => 'attribute',
                'description'  => 'Birth year of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'City name',
                'name'         => 'cityName',
                'slug'         => 'city-name',
                'type'         => 'attribute',
                'description'  => 'City name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Country name',
                'name'         => 'countryName',
                'slug'         => 'country-name',
                'type'         => 'attribute',
                'description'  => 'Country name of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Email',
                'name'         => 'email',
                'slug'         => 'email',
                'type'         => 'attribute',
                'description'  => 'Email of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Gender',
                'name'         => 'gender',
                'slug'         => 'gender',
                'type'         => 'attribute',
                'description'  => 'Gender of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Phone',
                'name'         => 'phone',
                'slug'         => 'phone',
                'type'         => 'attribute',
                'description'  => 'Phone of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Street address',
                'name'         => 'streetAddress',
                'slug'         => 'street-address',
                'type'         => 'attribute',
                'description'  => 'Street address of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Zipcode',
                'name'         => 'zipcode',
                'slug'         => 'zipcode',
                'type'         => 'attribute',
                'description'  => 'Zipcode of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Profile',
                'name'         => 'profile',
                'slug'         => 'profile',
                'type'         => 'attribute',
                'description'  => 'Profile attribute of a user.',
                'handler_id'   => 1
            ],

            // Flags
            [
                'display_name' => 'First name mismatch',
                'name'         => 'firstNameMismatch',
                'slug'         => 'first-name-mismatch',
                'type'         => 'flag',
                'description'  => 'First name mismatch of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last name mismatch',
                'name'         => 'lastNameMismatch',
                'slug'         => 'last-name-mismatch',
                'type'         => 'flag',
                'description'  => 'Last name mismatch of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Middle name mismatch',
                'name'         => 'middleNameMismatch',
                'slug'         => 'middle-name-mismatch',
                'type'         => 'flag',
                'description'  => 'Middle name mismatch of a user.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Compromised email',
                'name'         => 'compromisedEmail',
                'slug'         => 'compromised-email',
                'type'         => 'flag',
                'description'  => 'Compromised email used in user account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Dropbox empty',
                'name'         => 'dropboxEmpty',
                'slug'         => 'dropbox-empty',
                'type'         => 'flag',
                'description'  => 'Empty dropbox account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Facebook empty',
                'name'         => 'facebookEmpty',
                'slug'         => 'facebook-empty',
                'type'         => 'flag',
                'description'  => 'Empty facebook account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Google empty',
                'name'         => 'googleEmpty',
                'slug'         => 'google-empty',
                'type'         => 'flag',
                'description'  => 'Empty google account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Linkedin empty',
                'name'         => 'linkedinEmpty',
                'slug'         => 'linkedin-empty',
                'type'         => 'flag',
                'description'  => 'Empty linkedin account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Spotify empty',
                'name'         => 'spotifyEmpty',
                'slug'         => 'spotify-empty',
                'type'         => 'flag',
                'description'  => 'Empty spotify account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Twitter empty',
                'name'         => 'twitterEmpty',
                'slug'         => 'twitter-empty',
                'type'         => 'flag',
                'description'  => 'Empty twitter account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Yahoo empty',
                'name'         => 'yahooEmpty',
                'slug'         => 'yahoo-empty',
                'type'         => 'flag',
                'description'  => 'Empty yahoo account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Empty account',
                'name'         => 'accountEmpty',
                'slug'         => 'account-empty',
                'type'         => 'flag',
                'description'  => 'One of the submitted accounts is empty.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New account',
                'name'         => 'accountNew',
                'slug'         => 'account-new',
                'type'         => 'flag',
                'description'  => 'One of the submitted accounts is new.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New facebook',
                'name'         => 'facebookNew',
                'slug'         => 'facebook-new',
                'type'         => 'flag',
                'description'  => 'A new facebook account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New google',
                'name'         => 'googleNew',
                'slug'         => 'google-new',
                'type'         => 'flag',
                'description'  => 'A new google account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New paypal',
                'name'         => 'paypalNew',
                'slug'         => 'paypal-new',
                'type'         => 'flag',
                'description'  => 'A new paypal account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New twitter',
                'name'         => 'twitterNew',
                'slug'         => 'twitter-new',
                'type'         => 'flag',
                'description'  => 'A new twitter account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'New yahoo',
                'name'         => 'yahooNew',
                'slug'         => 'yahoo-new',
                'type'         => 'flag',
                'description'  => 'A new yahoo account.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Recent name changes',
                'name'         => 'recentNameChanges',
                'slug'         => 'recent-name-changes',
                'type'         => 'flag',
                'description'  => 'Recent name changes.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Recent facebook name changes',
                'name'         => 'recentNameChangesFacebook',
                'slug'         => 'recent-name-changes-facebook',
                'type'         => 'flag',
                'description'  => 'Recent facebook name changes.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Recent google name changes',
                'name'         => 'recentNameChangesGoogle',
                'slug'         => 'recent-name-changes-google',
                'type'         => 'flag',
                'description'  => 'Recent google name changes.',
                'handler_id'   => 1
            ],

            // Scores
            [
                'display_name' => 'Birth day score',
                'name'         => 'birthDayScore',
                'slug'         => 'birth-day-score',
                'type'         => 'score',
                'description'  => 'Birth day score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth month score',
                'name'         => 'birthMonthScore',
                'slug'         => 'birth-month-score',
                'type'         => 'score',
                'description'  => 'Birth month score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth year score',
                'name'         => 'birthYearScore',
                'slug'         => 'birth-year-core',
                'type'         => 'score',
                'description'  => 'Birth year score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'City name score',
                'name'         => 'cityNameScore',
                'slug'         => 'city-name-score',
                'type'         => 'score',
                'description'  => 'City name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Country name score',
                'name'         => 'countryNameScore',
                'slug'         => 'country-name-score',
                'type'         => 'score',
                'description'  => 'Country name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Email score',
                'name'         => 'emailScore',
                'slug'         => 'email-score',
                'type'         => 'score',
                'description'  => 'Email score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'First name score',
                'name'         => 'firstNameScore',
                'slug'         => 'first-name-score',
                'type'         => 'score',
                'description'  => 'First name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Gender score',
                'name'         => 'genderScore',
                'slug'         => 'gender-score',
                'type'         => 'score',
                'description'  => 'Gender score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last name score',
                'name'         => 'lastNameScore',
                'slug'         => 'last-name-score',
                'type'         => 'score',
                'description'  => 'Last name score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Phone score',
                'name'         => 'phoneScore',
                'slug'         => 'phone-score',
                'type'         => 'score',
                'description'  => 'Phone score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Street address score',
                'name'         => 'streetAddressScore',
                'slug'         => 'street-address-score',
                'type'         => 'score',
                'description'  => 'Street address score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Zipcode score',
                'name'         => 'zipcodeScore',
                'slug'         => 'zipcode-score',
                'type'         => 'score',
                'description'  => 'Zipcode score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'No Chargeback low score',
                'name'         => 'noChargebackScoreLow',
                'slug'         => 'no-chargeback-score-low',
                'type'         => 'score',
                'description'  => 'No Chargeback low score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'No Chargeback medium score',
                'name'         => 'noChargebackScoreMedium',
                'slug'         => 'no-chargeback-score-medium',
                'type'         => 'score',
                'description'  => 'No Chargeback medium score.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'No Chargeback high score',
                'name'         => 'noChargebackScoreHigh',
                'slug'         => 'no-chargeback-score-high',
                'type'         => 'score',
                'description'  => 'No Chargeback high score.',
                'handler_id'   => 1
            ],
            // Gates
            [
                'display_name' => 'No Chargeback',
                'name'         => 'noChargebackGate',
                'slug'         => 'no-chargeback-gate',
                'type'         => 'gate',
                'description'  => 'Displays the likelihood a user will not commit chargeback fraud.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth day',
                'name'         => 'birthDayGate',
                'slug'         => 'birth-day-gate',
                'type'         => 'gate',
                'description'  => 'Birth day gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth month',
                'name'         => 'birthMonthGate',
                'slug'         => 'birth-month-gate',
                'type'         => 'gate',
                'description'  => 'Birth month gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Birth year',
                'name'         => 'birthYearGate',
                'slug'         => 'birth-year-gate',
                'type'         => 'gate',
                'description'  => 'Birth year gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'City name',
                'name'         => 'cityNameGate',
                'slug'         => 'city-name-gate',
                'type'         => 'gate',
                'description'  => 'City name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Country name',
                'name'         => 'countryNameGate',
                'slug'         => 'country-name-gate',
                'type'         => 'gate',
                'description'  => 'Country name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Email',
                'name'         => 'emailGate',
                'slug'         => 'email-gate',
                'type'         => 'gate',
                'description'  => 'Email gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'First name',
                'name'         => 'firstNameGate',
                'slug'         => 'first-name-gate',
                'type'         => 'gate',
                'description'  => 'First name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Gender',
                'name'         => 'genderGate',
                'slug'         => 'gender-gate',
                'type'         => 'gate',
                'description'  => 'Gender gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Last Name',
                'name'         => 'lastNameGate',
                'slug'         => 'last-name-gate',
                'type'         => 'gate',
                'description'  => 'Last Name gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Phone',
                'name'         => 'phoneGate',
                'slug'         => 'phone-gate',
                'type'         => 'gate',
                'description'  => 'Phone gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Street address',
                'name'         => 'streetAddressGate',
                'slug'         => 'street-address-gate',
                'type'         => 'gate',
                'description'  => 'Street address gate.',
                'handler_id'   => 1
            ],
            [
                'display_name' => 'Zipcode',
                'name'         => 'zipcodeGate',
                'slug'         => 'zipcode-gate',
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
