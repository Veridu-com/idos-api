<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Hook;

use Test\Functional\AbstractFunctional;

class GetOneTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1321189817';
    }

    public function testSuccess() {
        $request = $this->createRequest($this->createEnvironment([
            'REQUEST_URI' => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1321189817'
        ]));
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'hook/getOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testErrorNotFound() {
        $environment = $this->createEnvironment([
            'REQUEST_URI' => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/0',
        ]);

        $request = $this->createRequest($environment);

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($body['status']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );
    }

    public function testErrorCredentialDoesntBelongToCompany() {
        $environment = $this->createEnvironment([
            'REQUEST_URI' => '/1.0/companies/veridu-ltd/credentials/1e772b1e4d57560422e07565600aca48/hooks/1321189817',
        ]);

        $request = $this->createRequest($environment);

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($body['status']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );
    }

    public function testErrorTargetCompanyDifferentFromActingCompany() {
        $environment = $this->createEnvironment([
            'REQUEST_URI' => '/1.0/companies/app-deck/credentials/1e772b1e4d57560422e07565600aca48/hooks/1321189817',
        ]);

        $request = $this->createRequest($environment);

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertFalse($body['status']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );
    }

    public function testErrorHookDoesntBelongToCredential() {
        $environment = $this->createEnvironment([
            'REQUEST_URI' => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1860914067',
        ]);

        $request = $this->createRequest($environment);

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($body['status']);
        /*
         * Validates Json Schema against Json Response'
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
