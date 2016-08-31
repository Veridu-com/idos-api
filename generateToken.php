<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lcobucci\JWT;

function generateToken() : string {
        $jwtParser     = new JWT\Parser();
        $jwtValidation = new JWT\ValidationData();
        $jwtSigner     = new JWT\Signer\Hmac\Sha256();
        $jwtBuilder    = new JWT\Builder();

        $jwtBuilder->set('iss', md5('public'));
        $jwtBuilder->set('sub', md5('JohnDoe1'));

        return (string) $jwtBuilder
            ->sign($jwtSigner, md5('private'))
            ->getToken();
    }

print_r(generateToken());

