<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Traits;

trait HasAuthCredentialToken {
    public function testWrongCredentials() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'    => $this->uri,
                'REQUEST_METHOD' => $this->httpMethod,
                'QUERY_STRING'   => 'credentialToken=dummy'
            ]
        );
        $request  = $this->createRequest($environment);
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema against Json Response
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