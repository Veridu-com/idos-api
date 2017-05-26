<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\HandlerService;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class CreateNewTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies/veridu-ltd/handlers/1321189817/handler-services';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'    => 'idOS Test handler 12x3change',
                    'url'     => 'http://google.com',
                    'listens' => [
                        'test',
                        'tes2'
                    ],
                    'privacy' => 3
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body['data']);
        $this->assertEquals(1806869811, $body['data']['id']);
        $this->assertSame('idOS Test handler 12x3change', $body['data']['name']);
        $this->assertSame('http://google.com', $body['data']['url']);
        $this->assertSame(['test', 'tes2'], $body['data']['listens']);
        $this->assertTrue($body['data']['enabled']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'handlerService/createNew.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testSuccessWithoutNullableValues() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name' => 'idOS Test handler 12x3change',
                    'url'  => 'http://google.com'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'handlerService/createNew.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );

        $this->assertNotEmpty($body['data']);
        $this->assertEquals(1003809286, $body['data']['id']);
        $this->assertSame('idOS Test handler 12x3change', $body['data']['name']);
        $this->assertSame('http://google.com', $body['data']['url']);
        $this->assertEmpty($body['data']['listens']);
        $this->assertTrue($body['data']['enabled']);
    }

    public function testNoNameThrowsException() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'url'     => 'http://google.com',
                    'listens' => [
                        'test',
                        'tes2'
                    ]
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testNoUrlThrowsException() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'    => 'idOS Test handler 12x3change',
                    'listens' => [
                        'test',
                        'tes2'
                    ]
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());
    }
}
