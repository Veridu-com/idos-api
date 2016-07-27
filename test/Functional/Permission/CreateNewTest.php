<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Permission;

use Slim\Http\Response;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class UpdateOneTest extends AbstractFunctional {
    // use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod   = 'POST';
        $this->uri          = '/1.0/companies/veridu-ltd/permissions';
    }

    public function testSuccess() {
        $env = $this->createEnvironment([
            'HTTP_CONTENT_TYPE' => 'application/json'
        ]);

        $request    = $this->createRequest($env, json_encode(['routeName' => 'hello:biscuit']));
        $response   = $this->process($request);
        $body       = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertEquals('hello:biscuit', $body['data']['route_name']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/createNew.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testNotFound() {
        return;
        $this->uri = sprintf('/1.0/companies/veridu-ltd/permissions/%s', 'not-a-route-name');

        $request            = $this->createRequest($this->createEnvironment());
        $response           = $this->process($request);
        $body               = json_decode($response->getBody(), true);

        // assertions
        $this->assertNotEmpty($body);
//        $this->assertEquals(404, $response->getStatusCode());
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
