<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Permission;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteAllTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri = '/1.0/companies/veridu-ltd/permissions';
        $this->populate($this->uri);
    }

    public function testSuccess() {
        // gets the number of related permissions
        $totalNumberOfEntities   = sizeof($this->populate($this->uri));  

        // then creates the DELETE request
        $request    = $this->createRequest($this->createEnvironment());
        $response   = $this->process($request);
        $body       = json_decode($response->getBody(), true);

        // success assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertEquals($totalNumberOfEntities, $body['deleted']); // checks if listAll retrived the number of deleted objects

        $this->populate($this->uri); // refreshes the $entities prop
        $this->assertEquals(0, sizeof($this->entities)); // checks if all entities were deleted

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/deleteAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
    
}
