<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Helper;

use Lcobucci\JWT;

/**
 * JWT Generator Class.
 */
class Token {
    /**
     * Generates a JWT Token.
     *
     * @param mixed  $subject
     * @param string $issuer
     * @param string $secret
     *
     * @return string
     */
    private static function generateJwtToken(
        $subject,
        string $issuer,
        string $secret
    ) : string {
        $jwtSigner  = new JWT\Signer\Hmac\Sha256();
        $jwtBuilder = new JWT\Builder();

        $jwtBuilder->set('iss', $issuer);

        if (! empty($subject)) {
            $jwtBuilder->set('sub', $subject);
        }

        return (string) $jwtBuilder
            ->sign($jwtSigner, $secret)
            ->getToken();
    }

    /**
     * Generates a User Token.
     *
     * A user token (UT) is a User's grant to be impersonate by a credential.
     * It must be issued by a credential (auto generation using a JWT library) or
     * as a SSO Endpoint response.
     * The UT identifies a user within a credential scope and can be used by the user
     * to interact with his/her own data or third party data.
     *
     * Token details:
     *  - Issuer: Credential's Public Key
     *  - Subject: Username
     *  - Signature secret: Credential's Secret Key
     *
     * @param string $userName          The token subject (username)
     * @param string $credentialPubKey  The token issuer (credential's public key)
     * @param string $credentialPrivKey The token signature secret (credential's secret key)
     *
     * @see \App\Repository\UserInterface
     *
     * @return string
     */
    public static function generateUserToken(
        string $userName,
        string $credentialPubKey,
        string $credentialPrivKey
    ) : string {
        return self::generateJwtToken(
            $userName,
            $credentialPubKey,
            $credentialPrivKey
        );
    }

    /**
     * Generates a Company Token.
     *
     * A company token (CoT) is a Company's grant to be impersonated by a user or by itself.
     * It must be issued by a company (auto generation using a JWT library; requires Company
     * secret to be shared - partners only?) or as a Token Endpoint response.
     * The CoT identifies a user (with its credential scope; or a company) and can be used by
     * the user (or the company) to perform management operations.
     *
     * Token details:
     *  - Issuer: Company's Public Key
     *  - Subject:
     *   1. Credential's Public Key followed by a colon and the username (user identification;
     *   Role based on user)
     *   2. Empty (company identification; Role = COMPANY)
     *  - Signature secret: Company's Secret Key
     *
     * @param mixed  $subject        The token subject (credential's public key followed by a
     *                               colon and the username or null)
     * @param string $companyPubKey  The token issuer (company's public key)
     * @param string $companyPrivKey The token signature secret (company's secret key)
     *
     * @return string
     */
    public static function generateCompanyToken(
        $subject,
        string $companyPubKey,
        string $companyPrivKey
    ) : string {
        return self::generateJwtToken(
            $subject,
            $companyPubKey,
            $companyPrivKey
        );
    }

    /**
     * Generates a Credential Token.
     *
     * A credential token is a Credential's grant to be impersonated by a handler.
     * It must be issued by a handler (auto generation using a JWT library).
     * The CrT identifies a credential and can be used by the handler to
     *
     * Token details:
     *  - Issuer: Handler's Public Key
     *  - Subject: Credential's Public Key
     *  - Signature secret: Handler's Private Key
     *
     * @param string $credentialPubKey The token subject (credential's public key)
     * @param string $handlerPubKey    The token issuer (handler's public key)
     * @param string $handlerPrivKey   The token signature secret (handler's private key)
     *
     * @see \App\Repository\CredentialInterface
     *
     * @return string
     */
    public static function generateCredentialToken(
        string $credentialPubKey,
        string $handlerPubKey,
        string $handlerPrivKey
    ) : string {
        return self::generateJwtToken(
            $credentialPubKey,
            $handlerPubKey,
            $handlerPrivKey
        );
    }
}
