<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Middleware\Auth;

use App\Middleware\Auth;
use App\Repository\DBCompany;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HandleCompanyTokenTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/';

    }

    public function testSuccess() {
        $token = DBCompany::generateToken(implode(':', [md5('public'), 'JohnDoe']), md5('dtl-udirev'), md5('veridu-ltd'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                $company = $request->getAttribute('company');
                $actingUser = $request->getAttribute('actingUser');
                $credential = $request->getAttribute('credential');

                $data = [
                    'company'    => $company->serialize(),
                    'actingUser' => $actingUser->serialize(),
                    'credential' => $credential->serialize()
                ];

                return $response->withJson($data, 200);
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame('veridu-ltd', $body['company']['slug']);
        $this->assertSame(md5('veridu-ltd'), $body['company']['public_key']);
        $this->assertSame('secure:' . md5('dtl-udirev'), $body['company']['private_key']);

        $this->assertSame('JohnDoe', $body['actingUser']['username']);
        $this->assertSame($body['credential']['id'], $body['actingUser']['credential_id']);

        $this->assertSame(md5('public'), $body['credential']['public']);
        $this->assertSame($body['company']['id'], $body['credential']['company_id']);
    }

    public function testSuccessNoSubject() {
        $token = DBCompany::generateToken(null, md5('dtl-udirev'), md5('veridu-ltd'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                $company = $request->getAttribute('company');
                $actingUser = $request->getAttribute('actingUser');
                $credential = $request->getAttribute('credential');

                $data = [
                    'company'    => $company->serialize(),
                    'actingUser' => $actingUser !== null ? true : false,
                    'credential' => $credential !== null ? true : false
                ];

                return $response->withJson($data, 200);
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame('veridu-ltd', $body['company']['slug']);
        $this->assertSame(md5('veridu-ltd'), $body['company']['public_key']);
        $this->assertSame('secure:' . md5('dtl-udirev'), $body['company']['private_key']);

        $this->assertFalse($body['actingUser']);
        $this->assertFalse($body['credential']);
    }

    public function testInvalidToken() {
        $token = 'invalid.token';

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Token', $body['error']['message']);
    }

    public function testInvalidCredential() {
        $token = DBCompany::generateToken(implode(':', [md5('public'), 'JohnDoe']), md5('dtl-udirev'), md5('invalid-public'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Company', $body['error']['message']);
    }

    public function testInvalidTokenSign() {
        $token = DBCompany::generateToken(implode(':', [md5('public'), 'JohnDoe']), md5('invalid-private'), md5('veridu-ltd'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Token Verification Failed', $body['error']['message']);
    }

    public function testInvalidSubjectFormat() {
        $token = DBCompany::generateToken(implode('.', [md5('public'), 'JohnDoe']), md5('dtl-udirev'), md5('veridu-ltd'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Subject', $body['error']['message']);
    }

    public function testInvalidSubjectCredential() {
        $token = DBCompany::generateToken(implode(':', [md5('invalid-public'), 'JohnDoe']), md5('dtl-udirev'), md5('veridu-ltd'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Credential Public Key', $body['error']['message']);
    }

    public function testInvalidSubjectUsername() {
        $token = DBCompany::generateToken(implode(':', [md5('public'), 'John*Doe']), md5('dtl-udirev'), md5('veridu-ltd'));

        $container      = $this->getApp()->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::COMP_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'companyToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Subject Username', $body['error']['message']);
    }
}
