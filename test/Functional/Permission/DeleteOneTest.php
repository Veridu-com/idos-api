<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Permission;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class DeleteOneTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    /**
     * Deleted endpoint property, initialized setUp().
     */
    private $deletedEndpoint;

    protected function setUp() {
        parent::setUp();

        $this->deletedEndpoint = [
            'uri'        => '/1.0/companies',
            'httpMethod' => 'GET',
            'delete_uri' => '/1.0/companies/veridu-ltd/permissions/companies:listAll'
        ];
        $this->httpMethod = 'DELETE';
        $this->uri        = $this->deletedEndpoint['delete_uri'];
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body             = json_decode((string) $response->getBody(), true);
        $numberOfEntities = count($this->entities); // total number of entities
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/deleteOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );

        $this->checkEntityDoesNotExist();

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
    }

    /**
     * Tries to assert current entity does not exist after the deletion.
     */
    public function checkEntityDoesNotExist() {
        // tries to fetch the deleted entity to ensure it was successfully deleted
        $getOneEnvironment = $this->createEnvironment(
            [
                'REQUEST_URI'        => $this->uri,
                'REQUEST_METHOD'     => 'GET',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $getOneRequest  = $this->createRequest($getOneEnvironment);
        $getOneResponse = $this->process($getOneRequest);
        $this->assertSame(404, $getOneResponse->getStatusCode());

        $getOneBody = json_decode((string) $getOneResponse->getBody(), true);
        $this->assertNotEmpty($getOneBody);

        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $getOneResponse->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = '/1.0/companies/veridu-ltd/permissions/not:route';
        $request   = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/deleteOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
