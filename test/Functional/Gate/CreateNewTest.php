<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Gate;

use App\Helper\Token;
use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;
    /**
     * @FIXME The HasAuthCredentialToken runs a wrong credentials test
     *        but we don't generate tokens yet, so there are no wrong credentials
     *        when token generations is implemented, please fix this by uncommenting the next line
     */
    // use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/9fd9f63e0d6487537569075da85a0c7f/gates';
        $this->token      = Token::generateCredentialToken(
            '4c9184f37cff01bcdc32dc486ec36961', // Credential id 1 public key
            '2c17c6393771ee3048ae34d6b380c5ec', // Credential id 1 private key
            '4c9184f37cff01bcdc32dc486ec36961'  // Credential id 1 public key
        );
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'QUERY_STRING'      => sprintf('credentialToken=%s', $this->token)
            ]
        );

        $name    = 'Testing';
        $pass    = true;
        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'name'  => $name,
                    'pass' => $pass
                ]
            )
        );
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($pass, $body['data']['pass']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema('gate/createNew.json', json_decode($response->getBody())),
            $this->schemaErrors
        );
    }
}
