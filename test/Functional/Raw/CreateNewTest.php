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

class CreateNewTest extends AbstractRawFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsCompanyToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/1321189817/raw';
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
                    'collection' => 'collection-test',
                    'data'       => 'value-test'
                ]
            )
        );

        $response = $this->process($request);

        $this->assertSame(201, $response->getStatusCode());

        $body     = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('collection-test', $body['data']['collection']);
        $this->assertSame('value-test', $body['data']['data']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/createNew.json',
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
                    'collection' => '',
                    'data'       => 'value-test'
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
