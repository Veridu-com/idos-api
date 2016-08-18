<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Service;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/services';
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
                    'name'          => 'New service name',
                    'url'           => 'http://service-url.com',
                    'enabled'       => true,
                    'access'        => 1,
                    'auth_username' => 'idos',
                    'auth_password' => 'secret',
                    'listens'       => [
                        'source.add.facebook'
                    ],
                    'triggers' => [
                        'source.scraper.facebook.finished'
                    ]
                ]
            )
        );

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertNotEmpty($body['data']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'service/createNew.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );
    }
}
