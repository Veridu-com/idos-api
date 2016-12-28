<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Feature;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class UpsertBulkTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'PUT';
        $this->uri        = sprintf('/1.0/profiles/%s/features/bulk', $this->userName);
    }

    public function testSuccessNoUpsert() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'name'      => 'test',
                'source_id' => 1321189817,
                'type'      => 'string',
                'value'     => 'testing'
            ],
            [
                'name'      => 'secondTest',
                'source_id' => 1321189817,
                'type'      => 'string',
                'value'     => 'testing2'
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($array)
        );

        $response = $this->process($request);
        // exit;
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);

        $this->assertTrue($body['status']);
    }

    public function testEmptyName() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'source_id' => 1321189817,
                'name'      => '',
                'type'      => 'string',
                'value'     => 'testing'
            ],
            [
                'source_id' => 1321189817,
                'name'      => '',
                'type'      => 'string',
                'value'     => 'testing2'
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($array)
        );
        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testEmptyValue() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'source_id' => 1321189817,
                'name'      => 'test1',
                'type'      => 'string',
                'value'     => null
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'test2',
                'type'      => 'string',
                'value'     => null
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
    }

    public function testBooleanValue() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'source_id' => 1321189817,
                'name'      => 'test1',
                'type'      => 'string',
                'value'     => true
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'test2',
                'type'      => 'string',
                'value'     => true
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
    }

    public function testFloatValue() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'source_id' => 1321189817,
                'name'      => 'test1',
                'type'      => 'string',
                'value'     => 1.2
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'test2',
                'type'      => 'string',
                'value'     => 1.2
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
    }

    public function testIntValue() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'source_id' => 1321189817,
                'name'      => 'test1',
                'type'      => 'string',
                'value'     => 10
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'test2',
                'type'      => 'string',
                'value'     => 7
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
    }

    public function testInvalidName() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'source_id' => 1321189817,
                'name'      => 'test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test',
                'type'      => 'string',
                'value'     => 'testing'
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}