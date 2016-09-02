<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Permission;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresCompanyToken;

class GetOneTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCompanyToken;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->populate(
            '/1.0/companies/veridu-ltd/permissions',
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );
        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/companies/veridu-ltd/permissions/%s', $this->entity['route_name']);
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($this->entity, $body['data']); // asserts it fetches the right entity

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'permission/getOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testNotFound() {
        $this->uri = '/1.0/companies/veridu-ltd/permissions/notA:routeName';

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
                ]
            )
        );
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

    public function testInvalidRouteName() {
        $this->uri = '/1.0/companies/veridu-ltd/permissions/invalidRouteName';

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(500, $response->getStatusCode());

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
