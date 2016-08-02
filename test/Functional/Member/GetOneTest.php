<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Member;

use Test\Functional\AbstractFunctional;

class GetOneTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/companies/veridu-ltd/members/9fd9f63e0d6487537569075da85a0c7f2';
    }

    public function testSuccess() {
        $request    = $this->createRequest($this->createEnvironment());
        $response   = $this->process($request);
        $body       = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'member/getOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testNotFound() {
        $this->uri = '/1.0/companies/veridu-ltd/members/dummy';
        $request            = $this->createRequest($this->createEnvironment());
        $response           = $this->process($request);

        $body               = json_decode($response->getBody(), true);

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
