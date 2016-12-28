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

class UpsertOneTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'PUT';
        $this->uri        = sprintf('/1.0/profiles/%s/features', $this->userName);
    }

    public function testSuccessNoUpsert() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'test';
        $type    = 'string';
        $value   = 'testing';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'source_id' => 1321189817,
                    'name'      => $name,
                    'type'      => $type,
                    'value'     => $value
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

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

        $name    = '';
        $value   = 'testing';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'name'  => $name,
                    'value' => $value
                ]
            )
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

        $name    = 'empty-feature';
        $type    = 'string';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'source_id' => 1321189817,
                    'name'      => $name,
                    'type'      => $type,
                    'value'     => null
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
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

        $name    = 'boolean-feature';
        $type    = 'boolean';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'source_id' => 1321189817,
                    'name'      => $name,
                    'type'      => $type,
                    'value'     => true
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
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

        $name    = 'float-feature';
        $type    = 'double';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'source_id' => 1321189817,
                    'name'      => $name,
                    'type'      => $type,
                    'value'     => 1.2
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
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

        $name    = 'int-feature';
        $type    = 'integer';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'source_id' => 1321189817,
                    'name'      => $name,
                    'type'      => $type,
                    'value'     => 10
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
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

        $name    = 'Name name name name name name name name name name name name name';
        $value   = 'testing';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'name'  => $name,
                    'value' => $value
                ]
            )
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