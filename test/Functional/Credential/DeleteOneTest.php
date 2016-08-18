<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Credential;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;
use Test\Functional\Traits\HasAuthCompanyPrivKey;

class DeleteOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyPrivKey;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961';
    }

    public function testSuccess() {
        $request  = $this->createRequest($this->createEnvironment());
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);
        // assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'credential/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = '/1.0/management/credentials/dummy';
        $request   = $this->createRequest($this->createEnvironment());
        $response  = $this->process($request);
        $body      = json_decode($response->getBody(), true);

        // assertions
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
