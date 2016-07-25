<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Credential;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies/veridu-ltd/credentials';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'       => 'New Credential',
                    'production' => false
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame('New Credential', $body['data']['name']);
        $this->assertSame('new-credential', $body['data']['slug']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'credential/createNew.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri   = '/1.0/companies/dummy-ltd/credentials';
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'       => 'Very Secure',
                    'production' => false
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
