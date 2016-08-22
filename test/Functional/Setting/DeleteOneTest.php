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

class DeleteOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    /**
     * @FIXME The HasAuthCredentialToken runs a wrong credentials test
     *        but we don't generate tokens yet, so there are no wrong credentials
     *        when token generations is implemented, please fix this by uncommenting the next line
     */
    // use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
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
        $body     = json_decode($response->getBody(), true);

        // success assertions
        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'setting/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
