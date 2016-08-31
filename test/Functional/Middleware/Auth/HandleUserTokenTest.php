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

class HandleUserTokenTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/';

    }

    public function testSuccess() {
        $token = Token::generateUserToken(
            'JohnDoe',
            md5('public'),
            md5('private')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $user = $request->getAttribute('user');
                    $company = $request->getAttribute('company');
                    $credential = $request->getAttribute('credential');

                    $data = [
                        'user'       => $user->serialize(),
                        'company'    => $company->serialize(),
                        'credential' => $credential->serialize()
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::USER));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'userToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertSame('JohnDoe', $body['user']['username']);
        $this->assertSame($body['credential']['id'], $body['user']['credential_id']);
        $this->assertSame(md5('public'), $body['credential']['public']);
        $this->assertSame('secure:' . md5('private'), $body['credential']['private']);
        $this->assertSame($body['company']['id'], $body['credential']['company_id']);
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
            ->add($authMiddleware(Auth::USER));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'userToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Token', $body['error']['message']);
    }

    public function testInvalidCredential() {
        $token = Token::generateUserToken(
            'JohnDoe',
            md5('invalid-public'),
            md5('private')
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
            ->add($authMiddleware(Auth::USER));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'userToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Credential', $body['error']['message']);
    }

    public function testInvalidTokenSign() {
        $token = Token::generateUserToken(
            'JohnDoe',
            md5('public'),
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
            ->add($authMiddleware(Auth::USER));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'userToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Token Verification Failed', $body['error']['message']);
    }

    public function testEmptySubject() {
        $token = Token::generateUserToken(
            '',
            md5('public'),
            md5('private')
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
            ->add($authMiddleware(Auth::USER));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'userToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }

    public function testInvalidSubject() {
        $token = Token::generateUserToken(
            'invalid*subject',
            md5('public'),
            md5('private')
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
            ->add($authMiddleware(Auth::USER));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'userToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Subject Claim', $body['error']['message']);
    }
}
