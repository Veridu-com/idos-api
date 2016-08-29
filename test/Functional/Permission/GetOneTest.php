<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Permission;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCompanyPrivKey;
use Test\Functional\Traits\HasAuthMiddleware;

class GetOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyPrivKey;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->populate('/1.0/companies/veridu-ltd/permissions');
        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/companies/veridu-ltd/permissions/%s', $this->entity['route_name']);
    }

    public function testSuccess() {
        $request  = $this->createRequest($this->createEnvironment());
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($this->entity, $body['data']); // asserts it fetches the right entity

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/getOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/companies/veridu-ltd/permissions/%s', 'not-a-route-name');

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
