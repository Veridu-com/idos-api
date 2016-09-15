<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Source;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class UpdateOneTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresUserToken,
        Traits\RequiresCredentialToken,
        Traits\RejectsCompanyToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'PUT';
        $this->uri        = sprintf('/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/%s', 1321189817);
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $request  = $this->createRequest($environment, json_encode(['name' => 'linkedin']));
        $response = $this->process($request);
        $status   = $response->getStatusCode();
        $this->assertSame(200, $status);

        $body = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('source-1', $body['data']['name']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'source/updateOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testOTPAttempts() {
        $status1 = ($this->proccessDefaultRequest())->getStatusCode();
        $status2 = ($this->proccessDefaultRequest())->getStatusCode();
        $status3 = ($this->proccessDefaultRequest())->getStatusCode();

        $this->assertSame(200, $status1);
        $this->assertSame(200, $status2);
        $this->assertSame(403, $status3);
    }

    private function proccessDefaultRequest() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $request = $this->createRequest($environment, json_encode(['name' => 'linkedin']));

        return $this->process($request);
    }

    public function testNotFound() {
        $this->uri = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/232983';

        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $request  = $this->createRequest($environment, json_encode(['name' => 'twitter']));
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
