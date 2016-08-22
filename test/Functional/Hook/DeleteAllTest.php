<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Hook;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteAllTest extends AbstractFunctional {
    use HasAuthMiddleware;
    /**
     * @FIXME The HasAuthCredentialToken runs a wrong credentials test
     *        but we don't generate tokens yet, so there are no wrong credentials
     *        when token generations is implemented, please fix this by uncommenting the next line
     */
    // use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks';
        // $this->populate($this->uri);
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'       => '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'QUERY_STRING'      => 'credentialToken=test'
            ]
        );

        $request = $this->createRequest($environment);

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
                'hook/deleteAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testErrorCredentialDoesntBelongToCompany() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'       => '/1.0/management/credentials/1e772b1e4d57560422e07565600aca48/hooks',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'QUERY_STRING'      => 'credentialToken=test'
            ]
        );

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
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'       => '/1.0/management/credentials/1e772b1e4d57560422e07565600aca48/hooks',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'QUERY_STRING'      => 'credentialToken=test'
            ]
        );

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
}
