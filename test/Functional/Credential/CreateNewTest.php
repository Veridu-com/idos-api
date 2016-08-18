<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Credential;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/management/credentials';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'QUERY_STRING' => 'credentialToken=test'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'       => 'New Credential',
                    'production' => false
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame('New Credential', $body['data']['name']);
        $this->assertSame('new-credential', $body['data']['slug']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'credential/createNew.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthMiddlewareEnvironment() {



        return $this->createEnvironment(
            [
                'REQUEST_URI'    => $this->uri,
                'REQUEST_METHOD' => $this->httpMethod,
                'QUERY_STRING'   => 'credentialToken=dummy'
            ]
        );
    }

    /**
     * @FIXME The code below used to rely on a company slug to define the target company
     *        Now we use credential tokens, but we do not generate them yet, so the code has been commented out
     *        After the implementation of credential token generation, please refactor this test to find the
     *        target company using the credential token
     */
    public function testNotFound() {
        // $this->uri   = '/1.0/management/credentials';
        // $environment = $this->createEnvironment(
        //     [
        //         'HTTP_CONTENT_TYPE' => 'application/json',
        //         'QUERY_STRING' => 'credentialToken=test',
        //     ]
        // );

        // $request = $this->createRequest(
        //     $environment,
        //     json_encode(
        //         [
        //             'name'       => 'Very Secure',
        //             'production' => false
        //         ]
        //     )
        // );

        // $response = $this->process($request);

        // $body = json_decode($response->getBody(), true);

        // $this->assertNotEmpty($body);

        // $this->assertEquals(404, $response->getStatusCode());
        // $this->assertFalse($body['status']);

        // /*
        //  * Validates Json Schema with Json Response
        //  */
        // $this->assertTrue(
        //     $this->validateSchema(
        //         'error.json',
        //         json_decode($response->getBody())
        //     ),
        //     $this->schemaErrors
        // );
    }
}
