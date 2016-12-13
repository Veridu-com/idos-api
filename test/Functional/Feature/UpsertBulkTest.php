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
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/features/bulk';
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
                'source_id' => 1321189817,
                'name'      => 'feature-test',
                'type'      => 'string',
                'value'     => 'testing'
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test2',
                'type'      => 'string',
                'value'     => 'testing2'
            ]
        ];

        $request = $this->createRequest(
            $environment, 
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($type, $body['data']['type']);
        $this->assertSame($value, $body['data']['value']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('feature/upsertOne.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
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
                'name'      => 'feature-test',
                'type'      => 'string',
                'value'     => null
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test2',
                'type'      => 'string',
                'value'     => null
            ]
        ];

        $request = $this->createRequest(
            $environment, 
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($type, $body['data']['type']);
        $this->assertNull($body['data']['value']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('feature/upsertBulk.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
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
                'name'      => 'feature-test',
                'type'      => 'string',
                'value'     => true
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test2',
                'type'      => 'string',
                'value'     => true
            ]
        ];

        $request = $this->createRequest(
            $environment, 
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($type, $body['data']['type']);
        $this->assertTrue($body['data']['value']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('feature/upsertBulk.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
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
                'name'      => 'feature-test',
                'type'      => 'string',
                'value'     => 1.2
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test2',
                'type'      => 'string',
                'value'     => 1.2
            ]
        ];

        $request = $this->createRequest(
            $environment, 
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($type, $body['data']['type']);
        $this->assertSame(1.2, $body['data']['value']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('feature/upsertBulk.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
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
                'name'      => 'feature-test',
                'type'      => 'string',
                'value'     => 10
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test2',
                'type'      => 'string',
                'value'     => 7
            ]
        ];

        $request = $this->createRequest(
            $environment, 
            json_encode($array)
        );


        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($type, $body['data']['type']);
        $this->assertSame(10, $body['data']['value']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('feature/upsertBulk.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
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
                'name'      => 'Name name name name name name name name name name name name name',
                'type'      => 'string',
                'value'     => 'testing'
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test2',
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
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testSuccessNoUpsertDuplicate() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $array = [
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test',
                'type'      => 'string',
                'value'     => 'testing'
            ],
            [
                'source_id' => 1321189817,
                'name'      => 'feature-test2',
                'type'      => 'string',
                'value'     => 'testing2'
            ]
        ];

        $request = $this->createRequest(
            $environment, 
            json_encode($array)
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($type, $body['data']['type']);
        $this->assertSame($value, $body['data']['value']);

        $this->assertTrue(
            $this->validateSchema('feature/upsertBulk.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );

        //Now, we are going to try to create the same feature again, and it must fail
        $response = $this->process($request);
        $this->assertSame(500, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}
