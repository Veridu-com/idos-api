<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Slim\Http\Response;
use Slim\Http\Uri;

class CreateNewTest extends AbstractRawFunctional {
    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/usr001/sources/1321189817/raw';
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
                    'name'  => 'name-test',
                    'data' => 'value-test'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('name-test', $body['data']['name']);
        $this->assertSame('value-test', $body['data']['data']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/createNew.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );

    }
}
