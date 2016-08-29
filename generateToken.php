<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lcobucci\JWT;

function generateToken($credentialPubKey, string $servicePrivKey, string $servicePubKey) : string {
        $jwtParser     = new JWT\Parser();
        $jwtValidation = new JWT\ValidationData();
        $jwtSigner     = new JWT\Signer\Hmac\Sha256();
        $jwtBuilder    = new JWT\Builder();

        $jwtBuilder->set('iss', $servicePubKey);
        $jwtBuilder->set('sub', $credentialPubKey);

        return (string) $jwtBuilder
            ->sign($jwtSigner, $servicePrivKey)
            ->getToken();
    }

print_r(generateToken("4c9184f37cff01bcdc32dc486ec36961", md5('private-1'), md5('public-1')));

