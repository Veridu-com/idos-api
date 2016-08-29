<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Hook;

use Test\Functional\AbstractFunctional;

class MainTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/';
    }

    public function testSuccess() {
        $request = $this->createRequest($this->createEnvironment());

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
