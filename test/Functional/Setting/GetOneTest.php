<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Setting;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class GetOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    /**
     * @FIXME The HasAuthCredentialToken runs a wrong credentials test
     *        but we don't generate tokens yet, so there are no wrong credentials
     *        when token generations is implemented, please fix this by uncommenting the next line
     */
    // use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->populate(
            '/1.0/management/settings',
            'GET',
            [
                'QUERY_STRING' => 'credentialToken=test'
            ]
        );
        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/management/settings/%s', $this->entity['id']);
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'credentialToken=test'
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($this->entity['section'], $body['data']['section']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'setting/getOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = '/1.0/management/settings/21321414';
        $request   = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'credentialToken=test'
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
