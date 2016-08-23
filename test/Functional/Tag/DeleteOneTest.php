<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Tag;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCredentialToken;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/profiles/9fd9f63e0d6487537569075da85a0c7f2/tags/user-2-tag-1';
        // $this->populate($this->uri);
    }

    public function testSuccess() {
        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialPrivKey=2c17c6393771ee3048ae34d6b380c5ec'
        ]));
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);
        // assertions
        $this->assertNotEmpty($body);
        $response->getStatusCode();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'tag/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/profiles/9fd9f63e0d6487537569075da85a0c7f2/tags/0000000');
        $request   = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialPrivKey=2c17c6393771ee3048ae34d6b380c5ec'
        ]));
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
                'tag/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

}
