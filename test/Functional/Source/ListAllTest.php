<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Source;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class ListAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresUserToken,
        Traits\RequiresCredentialToken,
        Traits\RejectsCompanyToken;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources';
    }

    public function testSuccessUserToken() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->userTokenHeader()
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(2, $body['data']);

        $this->assertSame(1321189817, $body['data'][0]['id']);
        $this->assertSame('source-1', $body['data'][0]['name']);
        $this->assertArrayHasKey('otp_check', $body['data'][0]['tags']);
        $this->assertSame('email', $body['data'][0]['tags']['otp_check']);

        $this->assertSame(517015180, $body['data'][1]['id']);
        $this->assertSame('source-2', $body['data'][1]['name']);
        $this->assertEquals(
            [
                'profile_id'   => 1234567890,
                'access_token' => '9dd4e461268c8034f5c8564e155c67a6'
            ],
            $body['data'][1]['tags']
        );
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'source/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testSuccessCredentialToken() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(2, $body['data']);

        $this->assertSame(1321189817, $body['data'][0]['id']);
        $this->assertSame('source-1', $body['data'][0]['name']);
        $this->assertArrayHasKey('otp_check', $body['data'][0]['tags']);
        $this->assertSame('email', $body['data'][0]['tags']['otp_check']);

        $this->assertSame(517015180, $body['data'][1]['id']);
        $this->assertSame('source-2', $body['data'][1]['name']);
        $this->assertEquals(
            [
                'profile_id'   => 1234567890,
                'access_token' => '9dd4e461268c8034f5c8564e155c67a6'
            ],
            $body['data'][1]['tags']
        );

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'source/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
