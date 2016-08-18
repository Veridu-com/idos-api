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
use Test\Functional\Traits\HasAuthCompanyPrivKey;

class ListAllFromSectionTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyPrivKey;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->populate('/1.0/companies/veridu-ltd/settings');
        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/companies/veridu-ltd/settings/%s', $this->entity['section']);
    }

    public function testSuccess() {
        $request  = $this->createRequest($this->createEnvironment());
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'setting/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFoundSlug() {
        $this->uri = '/1.0/companies/dummy-ltd/settings';
        $request   = $this->createRequest($this->createEnvironment());
        $response  = $this->process($request);
        $body      = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testInvalidSection() {
        $this->uri = '/1.0/companies/veridu-ltd/settings/section';
        $request   = $this->createRequest($this->createEnvironment());
        $response  = $this->process($request);
        $body      = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertEmpty($body['data']);

        /*
         * Validates Json Schema against Json Response
         */
         $this->assertTrue(
             $this->validateSchema(
                 'setting/listAll.json',
                 json_decode($response->getBody())
             ),
             $this->schemaErrors
         );
    }
}
