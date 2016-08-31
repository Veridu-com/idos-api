<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Mapped;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCredentialToken;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteOneTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/attributes/user1Attribute1/scores/user-1-attribute-1-score-1';
        // $this->populate($this->uri);
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request  = $this->createRequest($environment);
        $response = $this->process($request);

        $body     = json_decode($response->getBody(), true);
        
        $this->assertNotEmpty($body);
        $response->getStatusCode();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'score/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/attributes/user1Attribute1/scores/00000');
        $request   = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJlZjk3MGZmYWQxZjEyNTNhMjE4MmE4ODY2NzIzMzk5MSIsInN1YiI6IjRjOTE4NGYzN2NmZjAxYmNkYzMyZGM0ODZlYzM2OTYxIn0.oeiD9R7FlnMBiDW3UClRO39nvbMM-TTZkyedYaSysCc'
                ]
            )
        );
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        // assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'score/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
