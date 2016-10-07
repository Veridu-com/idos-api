<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\Traits;

class UpdateOneTest extends AbstractRawFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();
        $this->populateDb();

        $this->httpMethod = 'PUT';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/raw';
    }

    public function testSuccess() {
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
                    'source_id' => 1321189817,
                    'collection' => 'raw-1',
                    'data'       => ['test' => 'data2']
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('raw-1', $body['data']['collection']);
        $this->assertSame(['test' => 'data2'], $body['data']['data']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/upsert.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri   = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/raw';
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
                    'source_id' => 00000,
                    'collection' => 'raw-1',
                    'data'       => ['test' => 'data']
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

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

    public function testEmptyCollection() {
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
                    'source_id' => 1321189817,
                    'collection' => '',
                    'data'       => ['test' => 'data']
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
}
