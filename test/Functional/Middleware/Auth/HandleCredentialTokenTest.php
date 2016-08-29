<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Middleware\Auth;

use App\Middleware\Auth;
use App\Repository\DBCredential;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HandleCredentialTokenTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/';

    }

    public function testSuccess() {
        $token = DBCredential::generateToken(md5('public'), md5('private-1'), md5('public-1'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                $service = $request->getAttribute('service');
                $company = $request->getAttribute('company');
                $credential = $request->getAttribute('credential');

                $data = [
                    'service'    => $service->serialize(),
                    'company'    => $company->serialize(),
                    'credential' => $credential->serialize()
                ];

                return $response->withJson($data, 200);
            })
            ->add($authMiddleware(Auth::CRED_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(md5('public-1'), $body['service']['public']);
        $this->assertSame('secure:' . md5('private-1'), $body['service']['private']);

        $this->assertSame(md5('public'), $body['credential']['public']);
        $this->assertSame('secure:' . md5('private'), $body['credential']['private']);
        $this->assertSame($body['company']['id'], $body['credential']['company_id']);
    }

    public function testInvalidToken() {
        $token = 'invalid.token';

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::CRED_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Token', $body['error']['message']);
    }

    public function testInvalidService() {
        $token = DBCredential::generateToken(md5('public'), md5('private-1'), md5('invalid-service-public'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::CRED_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Service', $body['error']['message']);
    }

    public function testInvalidTokenSign() {
        $token = DBCredential::generateToken(md5('public'), md5('invalid-service-private'), md5('public-1'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::CRED_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Token Verification Failed', $body['error']['message']);
    }

    public function testNullSubject() {
        $token = DBCredential::generateToken(null, md5('private-1'), md5('public-1'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::CRED_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }

    public function testEmptySubject() {
        $token = DBCredential::generateToken('', md5('private-1'), md5('public-1'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::CRED_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }

    public function testInvalidSubject() {
        $token = DBCredential::generateToken(md5('invalid-credential-public'), md5('private-1'), md5('public-1'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::CRED_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Credential', $body['error']['message']);
    }
}
