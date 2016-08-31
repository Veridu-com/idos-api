<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Mapped;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCredentialToken;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteAllTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/attributes/user1Attribute1/scores';
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

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $response->getStatusCode();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'score/deleteAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
