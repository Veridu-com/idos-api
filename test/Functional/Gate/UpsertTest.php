<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Gate;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class UpsertTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'PUT';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/gates';
    }

    public function testCreated() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'             => 'Name Test',
                    'confidence_level' => 'high'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('Name Test', $body['data']['name']);
        $this->assertSame('name-test', $body['data']['slug']);
        $this->assertSame('high', $body['data']['confidence_level']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/upsert.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testUpdated() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'             => 'Name Test',
                    'confidence_level' => 'medium'
                ]
            )
        );

        $response = $this->process($request);

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'             => 'Name Test',
                    'confidence_level' => 'high'
                ]
            )
        );

        $response = $this->process($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('Name Test', $body['data']['name']);
        $this->assertSame('name-test', $body['data']['slug']);
        $this->assertSame('high', $body['data']['confidence_level']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/upsert.json',
                json_decode((string) $response->getBody())
            ),
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

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'             => '',
                    'confidence_level' => 'high'
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
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testEmptyConfidenceLevel() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name' => 'Name Test'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('Name Test', $body['data']['name']);
        $this->assertSame('name-test', $body['data']['slug']);
        $this->assertNull($body['data']['confidence_level']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/upsert.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
