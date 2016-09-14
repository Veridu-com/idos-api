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

class HandleCredentialTokenTest extends AbstractAuthFunctional {
    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->uri        = '/';

    }

    public function testSuccess() {
        $token = Token::generateCredentialToken(
            md5('public'),
            md5('public-1'),
            md5('private-1')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $service = $request->getAttribute('service');
                    $company = $request->getAttribute('company');
                    $credential = $request->getAttribute('credential');

                    $data = [
                    'service'    => $service->serialize(),
                    'company'    => $company->serialize(),
                    'credential' => $credential->serialize()
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::CREDENTIAL));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertSame(md5('public-1'), $body['service']['public']);
        $this->assertSame('secure:' . md5('private-1'), $body['service']['private']);
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
            ->add($authMiddleware(Auth::CREDENTIAL));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Token', $body['error']['message']);
    }

    public function testInvalidService() {
        $token = Token::generateCredentialToken(
            md5('public'),
            md5('invalid-service-public'),
            md5('private-1')
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
            ->add($authMiddleware(Auth::CREDENTIAL));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Service', $body['error']['message']);
    }

    public function testInvalidTokenSign() {
        $token = Token::generateCredentialToken(
            md5('public'),
            md5('public-1'),
            md5('invalid-service-private')
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
            ->add($authMiddleware(Auth::CREDENTIAL));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Token Verification Failed', $body['error']['message']);
    }

    public function testEmptySubject() {
        $token = Token::generateCredentialToken(
            '',
            md5('public-1'),
            md5('private-1')
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
            ->add($authMiddleware(Auth::CREDENTIAL));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }

    public function testInvalidSubject() {
        $token = Token::generateCredentialToken(
            md5('invalid-credential-public'),
            md5('public-1'),
            md5('private-1')
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
            ->add($authMiddleware(Auth::CREDENTIAL));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Credential', $body['error']['message']);
    }
}
