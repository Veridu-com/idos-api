<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Warning;

use App\Helper\Token;
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
        $this->token      = Token::generateCredentialToken(
            '4c9184f37cff01bcdc32dc486ec36961', // Credential id 1 public key
            '2c17c6393771ee3048ae34d6b380c5ec', // Credential id 1 private key
            '4c9184f37cff01bcdc32dc486ec36961'  // Credential id 1 public key
        );
        $this->userName = '9fd9f63e0d6487537569075da85a0c7f';

        $this->populate(
            sprintf('/1.0/profiles/%s/warnings', $this->userName),
            'GET',
            [
                'QUERY_STRING' => sprintf('credentialToken=%s', $this->token)
            ]
        );
        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/profiles/%s/warnings/%s', $this->userName, $this->entity['slug']);
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => sprintf('credentialToken=%s', $this->token)
                ]
            )
        );

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);
        // assertions
        $this->assertNotEmpty($body);
        $response->getStatusCode();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'warnings/deleteOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/profiles/%s/warnings/dummy-ltd', $this->userName);
        $request   = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => sprintf('credentialToken=%s', $this->token)
                ]
            )
        );
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

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
