<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Feature;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class CreateNewTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/features';
    }

    public function testSuccessNoUpsert() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'feature-test';
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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testEmptySource() {
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
                    'source_id' => null,
                    'name'      => $name,
                    'type'      => $type,
                    'value'     => null
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode(), (string) $response->getBody());

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

    public function testSuccessNoUpsertDuplicate() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name  = 'feature-test-2';
        $type  = 'string';
        $value = 'testing';

        //First, we are going to create a feature without upsert flag
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

        $this->assertTrue(
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
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
