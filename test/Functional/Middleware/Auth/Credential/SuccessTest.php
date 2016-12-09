<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth\Credential;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;

class SuccessTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        parent::setUp();
        $this->uri = '/successTest';
        $this->httpMethod = 'GET';
    }

    public function testSuccess() {
        $token = Token::generateCredentialToken(
            md5('public'),
            md5('public-1'),
            md5('private-1')
        );

        $authMiddleware = $this->middlewareApp
            ->getContainer()
            ->get('authMiddleware');
        $this->middlewareApp
            ->get(
                '/successTest', function (ServerRequestInterface $request, ResponseInterface $response) {
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
}
