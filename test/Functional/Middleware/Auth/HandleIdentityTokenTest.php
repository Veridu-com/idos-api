<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HandleCompanyTokenTest extends AbstractAuthFunctional {
    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->uri        = '/';
    }

    public function testSuccessQueryString() {
        $token = Token::generateIdentityToken(
            '5d41402abc4b2a76b9719d911017c592',
            '7d793037a0760186574b0282f2f435e7'
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');

        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $identity = $request->getAttribute('identity');

                    $data = [
                        'identity'    => $identity->serialize(),
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::IDENTITY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'identityToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertSame(
            '5d41402abc4b2a76b9719d911017c592',
            $body['identity']['public_key']
        );

        $this->assertSame(
            'secure:7d793037a0760186574b0282f2f435e7',
            $body['identity']['private_key']
        );
    }

    public function testSuccessHeader() {
        $token = Token::generateIdentityToken(
            '5d41402abc4b2a76b9719d911017c592',
            '7d793037a0760186574b0282f2f435e7'
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');

        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $identity = $request->getAttribute('identity');

                    $data = [
                        'identity'    => $identity->serialize(),
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::IDENTITY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => sprintf('IdentityToken %s', $token)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertSame(
            '5d41402abc4b2a76b9719d911017c592',
            $body['identity']['public_key']
        );

        $this->assertSame(
            'secure:7d793037a0760186574b0282f2f435e7',
            $body['identity']['private_key']
        );
    }

    public function testInvalidToken() {
        $token = 'invalid.token';

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');

        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::IDENTITY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'identityToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Token',
            $body['error']['message']
        );
    }

    public function testInvalidTokenSign() {
        $token = Token::generateIdentityToken(
            md5('5d41402abc4b2a76b9719d911017c592'),
            md5('invalid-private')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');

        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::IDENTITY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'identityToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Identity',
            $body['error']['message']
        );
    }
}
