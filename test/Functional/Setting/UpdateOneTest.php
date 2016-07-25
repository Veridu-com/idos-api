<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Setting;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class UpdateOneTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'PUT';
        $this->populate('/1.0/companies/veridu-ltd/settings');
        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/companies/veridu-ltd/settings/%s/%s', $this->entity['section'], $this->entity['property']);
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(['HTTP_CONTENT_TYPE' => 'application/json']);

        $request = $this->createRequest($environment, json_encode(['value' => 'New biscuit']));

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        // assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'setting/updateOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri   = sprintf('/1.0/companies/veridu-ltd/settings/%s/%s', 'dummy', $this->entity['property']);
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(['value' => 'New Prop. Value'])
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        // assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(404, $response->getStatusCode());
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
