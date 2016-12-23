<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth\Credential;

use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;

class SuccessTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        $this->uri           = '/testSuccess';
        $this->httpMethod    = 'GET';
    }

    public function testSuccess() {
        $token = $this->credentialToken();

        $authMiddleware = $this->middlewareApp
            ->getContainer()
            ->get('authMiddleware');
        $this->middlewareApp
            ->get(
                '/testSuccess', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $company = $request->getAttribute('company');
                    $credential = $request->getAttribute('credential');
                    $handler = $request->getAttribute('handler');

                    $data = [
                        'handler'    => $handler->serialize(),
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
        $this->assertSame('b16c931c061e14af275bd2c86d3cf48d', $body['handler']['public']);
        $this->assertNotEmpty($body['handler']['private']);
        $this->assertSame('4c9184f37cff01bcdc32dc486ec36961', $body['credential']['public']);
        $this->assertNotEmpty($body['credential']['private']);
        $this->assertSame($body['company']['id'], $body['credential']['company_id']);
    }
}
