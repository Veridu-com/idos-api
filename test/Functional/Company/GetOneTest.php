<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Company;

use Test\Functional\AbstractFunctional;

class GetOneTest extends AbstractFunctional {
    // REQUIRES NO AUTH :-)

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/companies/veridu-ltd';
    }

    public function testSuccess() {
        $request  = $this->createRequest($this->createEnvironment());
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'company/getOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testNotFound() {
        $this->uri = '/1.0/companies/dummy-ltd';

        $request  = $this->createRequest($this->createEnvironment());
        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
