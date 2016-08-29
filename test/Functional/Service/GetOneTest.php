<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Service;

use Test\Functional\AbstractFunctional;

class GetOneTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/services/1321189817';
    }

    public function testSuccess() {
        $request  = $this->createRequest($this->createEnvironment());
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'service/getOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testNotFound() {
        $this->uri = '/1.0/services/13211898171';

        $request  = $this->createRequest($this->createEnvironment());
        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
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
