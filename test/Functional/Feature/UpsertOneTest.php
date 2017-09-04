<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Feature;

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

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertSame($value, $body['data'][0]['value']);
    }

    public function testEmptySource() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'empty-source';
        $type    = 'string';
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'name'      => $name,
                    'type'      => $type,
                    'value'     => null
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());

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

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertNull($body['data'][0]['value']);
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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertNull($body['data'][0]['value']);
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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertTrue($body['data'][0]['value']);
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
        $value   = 1.2;
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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertSame($value, $body['data'][0]['value']);
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
        $value   = 10;
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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertSame($value, $body['data'][0]['value']);
    }

    public function testArrayValue() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'array-feature';
        $type    = 'array';
        $value   = ['value1', 'value2'];
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
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());

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

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertSame($value, $body['data'][0]['value']);
    }

    public function testArrayAsObjectValue() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'array-feature';
        $type    = 'array';
        $value   = ['key' => 'value'];
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
            $this->validateSchema('feature/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'REQUEST_METHOD'     => 'GET',
                    'REQUEST_URI'        => sprintf('%s?name=*%s*', $this->uri, $name)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($name, $body['data'][0]['name']);
        $this->assertSame($type, $body['data'][0]['type']);
        $this->assertSame($value, $body['data'][0]['value']);
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
