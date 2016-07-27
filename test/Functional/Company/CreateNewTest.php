<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Company;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest($environment, json_encode(['name' => 'New Company']));

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

            $this->assertNotEmpty($body);

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertTrue($body['status']);
        $this->assertSame('New Company', $body['data']['name']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'company/createNew.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );

    }
}
