<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Subscription;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class CreateNewTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken,
        Traits\RequiresIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961/subscriptions';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $data = [
            'gate_id' => '1321189817',
            'warning_id' => '1321189817'
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);

        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('subscription/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testInvalidGateId() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $data = [
            'gate_id' => '654',
            'warning_id' => '1321189817'
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);

        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testInvalidWarningId() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $data = [
            'warning_id' => '654',
            'gate_id' => '1321189817'
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);

        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}
