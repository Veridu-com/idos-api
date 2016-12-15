<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Recommendation;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class UpsertTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'PUT';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/recommendation';
    }

    public function testInsert() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment, json_encode(
                [
                'result'  => 'result-test',
                'passed'  => [],
                'failed'  => []
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('result-test', $body['data']['result']);
        $this->assertSame([], $body['data']['passed']);
        $this->assertSame([], $body['data']['failed']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'recommendation/upsert.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testUpdate() {
        $this->testInsert();

        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment, json_encode(
                [
                'result'  => 'result-test-2',
                'passed'  => [
                    'rule-1',
                    'rule-2',
                    'rule-3'
                ],
                'failed' => [
                    'rule-4',
                    'rule-5',
                    'rule-6'
                ]
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('result-test-2', $body['data']['result']);
        $this->assertSame(['rule-1', 'rule-2', 'rule-3'], $body['data']['passed']);
        $this->assertSame(['rule-4', 'rule-5', 'rule-6'], $body['data']['failed']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'recommendation/upsert.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
