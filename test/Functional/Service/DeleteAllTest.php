<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Service;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class DeleteAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/services';
        $this->populate($this->uri);
    }

    public function testSuccess() {
        // then creates the DELETE request
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        // checks if listAll retrived the number of deleted objects
        $this->assertSame(count($this->entities), $body['deleted']);
        // refreshes the $entities prop
        $this->populate($this->uri);
        // checks if all entities were deleted
        $this->assertCount(0, $this->entities);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'service/deleteAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
