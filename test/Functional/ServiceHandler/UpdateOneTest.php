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

class UpdateOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyPrivKey;

    protected function setUp() {
        $this->httpMethod = 'PUT';
        $this->uri        = '/1.0/service-handlers/email/veridu-email-handler';
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
                    'name'         => 'MyCompany x Handler',
                    'source'       => 'My lockd source',
                    'service'      => 'email',
                    'location'     => 'http://localhost:8001',
                    'authUsername' => 'sodi',
                    'authPassword' => 'terces'
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame('MyCompany x Handler', $body['data']['name']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'serviceHandler/updateOne.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );

    }

    public function testNotFound() {
        $this->uri = '/1.0/service-handlers/dummy/dummy-service-slug';

        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'         => 'MyCompany x Handler',
                    'source'       => 'My lockd source',
                    'service'      => 'email',
                    'location'     => 'http://localhost:8001',
                    'authUsername' => 'sodi',
                    'authPassword' => 'terces'
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

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
