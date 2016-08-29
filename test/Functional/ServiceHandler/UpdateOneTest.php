<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\ServiceHandler;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCompanyPrivKey;
use Test\Functional\Traits\HasAuthMiddleware;

class UpdateOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyPrivKey;

    protected function setUp() {
        $this->httpMethod = 'PUT';
        $this->uri        = '/1.0/service-handlers/1321189817';
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
                    'listens' => [
                        'idos:source.facebook.added'
                    ]
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame(['idos:source.facebook.added'], $body['data']['listens']);

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
        $this->uri = '/1.0/service-handlers/13211898171';

        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'listens' => [
                        'dummy:listens'
                    ]
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
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
