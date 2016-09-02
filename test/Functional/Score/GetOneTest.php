<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Source;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresCredentialToken;

class GetOneTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/attributes/user1Attribute1/scores/user1Attribute1Score1';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request  = $this->createRequest($environment);
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('user1Attribute1Score1', $body['data']['name']);
        $this->assertSame(1.2, $body['data']['name']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'score/getOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );

    }

    public function testNotFound() {
        $this->uri = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/attributes/user1Attribute1/scores/0000000';
        $request   = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
