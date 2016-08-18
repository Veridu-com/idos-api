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

    /**
     * Deleted endpoint property, initialized setUp().
     */
    private $deletedEndpoint;

    protected function setUp() {
        $this->deletedEndpoint = [
            'uri'        => '/1.0/companies',
            'httpMethod' => 'GET',
            'delete_uri' => '/1.0/companies/veridu-ltd/permissions/companies:listAll'
        ];
        $this->httpMethod = 'DELETE';
        $this->uri        = $this->deletedEndpoint['delete_uri'];
    }

    public function testSuccess() {
        $request          = $this->createRequest($this->createEnvironment());
        $response         = $this->process($request);
        $body             = json_decode($response->getBody(), true);
        $numberOfEntities = sizeof($this->entities); // total number of entities

        // assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

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

        $this->checkEntityDoesNotExist();
        $this->checkForbiddenAccessTo($this->deletedEndpoint['uri'], $this->deletedEndpoint['httpMethod']);
    }

    /**
     * Tries to assert forbidden access to given $uri, $method.
     *
     * @param string $uri URI of the route
     * @param string method HTTP method of the route
     */
    public function checkForbiddenAccessTo(string $uri, string $method) {
        $this->httpMethod = $method;
        $this->uri        = $uri;
        $request          = $this->createRequest($this->createEnvironment());
        $response         = $this->process($request);
        $body             = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertFalse($body['status']);
    }

    /**
     * Tries to assert current entity does not exist after the deletion.
     */
    public function checkEntityDoesNotExist() {
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
        $request   = $this->createRequest($this->createEnvironment());
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
                'permission/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

}
