<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Score;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;

class CreateNewTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/attributes/user1Attribute1/scores';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'QUERY_STRING'      => 'credentialToken=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJlZjk3MGZmYWQxZjEyNTNhMjE4MmE4ODY2NzIzMzk5MSIsInN1YiI6IjRjOTE4NGYzN2NmZjAxYmNkYzMyZGM0ODZlYzM2OTYxIn0.oeiD9R7FlnMBiDW3UClRO39nvbMM-TTZkyedYaSysCc'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'  => 'name-test',
                    'value' => 0.6
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertTrue($body['status']);
        $this->assertSame('name-test', $body['data']['name']);
        $this->assertSame(0.6, $body['data']['value']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'score/createNew.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );

    }
}
