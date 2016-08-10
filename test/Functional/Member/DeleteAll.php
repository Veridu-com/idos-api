<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Member;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteAllTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/companies/veridu-ltd/members';
        // $this->populate($this->uri);
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest($environment, json_encode(['credential' => '4c9184f37cff01bcdc32dc486ec36961']));

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
                'member/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
         $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest($environment, json_encode(['credential' => '0000000000000']));

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);
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
