<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Service;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class DeleteAllTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/services';
        $this->populate($this->uri);
    }

    public function testSuccess() {
        // then creates the DELETE request
        $request  = $this->createRequest();
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        // checks if listAll retrived the number of deleted objects
        $this->assertSame(count($this->entities), $body['deleted']);
        // refreshes the $entities prop
        $this->populate($this->uri);
        // checks if all entities were deleted
        $this->assertSame(0, count($this->entities));

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'service/deleteAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
