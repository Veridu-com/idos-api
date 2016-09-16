<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Hook;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class DeleteOneTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1321189817';
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'REQUEST_URI'        => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1321189817',
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()]
            )
        );
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'hook/deleteOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testErrorNotFound() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/0',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest($environment);

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testErrorCredentialDoesntBelongToCompany() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/companies/veridu-ltd/credentials/1e772b1e4d57560422e07565600aca48/hooks/1321189817',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest($environment);

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testErrorHookDoesntBelongToCredential() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1860914067',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest($environment);

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
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
