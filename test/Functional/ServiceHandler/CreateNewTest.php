<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\ServiceHandler;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;
use Test\Functional\Traits\HasAuthCompanyPrivKey;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyPrivKey;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/service-handlers';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'         => 'New Service Handler',
                    'source'       => 'unknown',
                    'service'      => 'email',
                    'location'     => 'http://localhost:8001',
                    'authUsername' => 'idos',
                    'authPassword' => 'secret'
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertNotEmpty($body['data']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'serviceHandler/createNew.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );
    }
}
