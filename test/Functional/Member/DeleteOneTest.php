<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Member;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCompanyPrivKey;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    /**
     * @FIXME The HasAuthCredentialToken runs a wrong credentials test
     *        but we don't generate tokens yet, so there are no wrong credentials
     *        when token generations is implemented, please fix this by uncommenting the next line
     */
    // use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/management/members/1321189817';
        // $this->populate($this->uri);
    }

    public function testSuccess() {
        $request  = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING'      => 'credentialToken=test'
                ]
            )
        );
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
                'member/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/management/members/000000');
        $request  = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING'      => 'credentialToken=test'
                ]
            )
        );
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
