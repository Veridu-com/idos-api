<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Helper;

use Lcobucci\JWT;

/**
 * Token Class.
 */
class Token {
    /**
     * @see \App\Repository\UserInterface
     */
    public static function generateUserToken(string $username, string $credentialPrivKey, string $credentialPubKey) : string {
        $jwtParser     = new JWT\Parser();
        $jwtValidation = new JWT\ValidationData();
        $jwtSigner     = new JWT\Signer\Hmac\Sha256();
        $jwtBuilder    = new JWT\Builder();

        $jwtBuilder->set('iss', $credentialPubKey);
        $jwtBuilder->set('sub', $username);

        return (string) $jwtBuilder
            ->sign($jwtSigner, $credentialPrivKey)
            ->getToken();
    }

    /**
     * @see \App\Repository\CredentialInterface
     */
    public static function generateCredentialToken(string $subjectCredentialPubKey, string $issuerCredentialPrivKey, string $issuerCredentialPubKey) : string {
        $jwtParser     = new JWT\Parser();
        $jwtValidation = new JWT\ValidationData();
        $jwtSigner     = new JWT\Signer\Hmac\Sha256();
        $jwtBuilder    = new JWT\Builder();

        $jwtBuilder->set('iss', $issuerCredentialPubKey);
        $jwtBuilder->set('sub', $subjectCredentialPubKey);

        return (string) $jwtBuilder
            ->sign($jwtSigner, $issuerCredentialPrivKey)
            ->getToken();
    }
}
