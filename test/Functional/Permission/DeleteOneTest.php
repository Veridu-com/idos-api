<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Permission;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteOneTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->populate('/1.0/companies/veridu-ltd/permissions');
        $this->entity = $this->getRandomEntity();
        $this->uri = sprintf('/1.0/companies/veridu-ltd/permissions/%s', $this->entity['route_name']);
    }

    public function testSuccess() {
        $request            = $this->createRequest($this->createEnvironment());
        $response           = $this->process($request);
        $body               = json_decode($response->getBody(), true);
        $numberOfEntities   = sizeof($this->entities); // total number of entities

        // assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertEquals(1, $body['deleted']); // checks if one was deleted

        // tries to fetch the deleted entity to ensure it was successfully deleted 
        $getOneEnvironment = $this->createEnvironment(
            [
                'REQUEST_URI'    => $this->uri,
                'REQUEST_METHOD' => 'GET'
            ]
        );

        $getOneRequest  = $this->createRequest($getOneEnvironment);
        $getOneResponse = $this->process($getOneRequest);
        $getOneBody     = json_decode($getOneResponse->getBody(), true);

        // error assertions
        $this->assertNotEmpty($getOneBody);
        $this->assertEquals(404, $getOneResponse->getStatusCode());

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );

        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode($getOneResponse->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/companies/veridu-ltd/permissions/%s', 'not-a-route-name');

        $request            = $this->createRequest($this->createEnvironment());
        $response           = $this->process($request);
        $body               = json_decode($response->getBody(), true);

        // assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertEquals(0, $body['deleted']); // checks if one was deleted

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

}
