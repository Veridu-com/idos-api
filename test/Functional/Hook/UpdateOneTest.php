<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Hook;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCompanyToken;
use Test\Functional\Traits\HasAuthMiddleware;

class UpdateOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyToken;

    protected function setUp() {
        $this->httpMethod = 'PUT';
        $this->uri        = '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1321189817';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'trigger'    => 'trigger.changed',
                    'url'        => 'http://changed.com/test.php',
                    'subscribed' => false
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('trigger.changed', $body['data']['trigger']);
        $this->assertSame('http://changed.com/test.php', $body['data']['url']);
        $this->assertSame(false, $body['data']['subscribed']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'hook/updateOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/0',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'trigger'    => 'trigger.changed',
                    'url'        => 'http://changed.com/test.php',
                    'subscribed' => false
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

    public function testErrorCredentialDoesntBelongToCompany() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/management/credentials/1e772b1e4d57560422e07565600aca48/hooks/1321189817',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'trigger'    => 'trigger.changed',
                    'url'        => 'http://changed.com/test.php',
                    'subscribed' => false
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema against Json Response'
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
                'REQUEST_URI'        => '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks/1860914067',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'trigger'    => 'trigger.changed',
                    'url'        => 'http://changed.com/test.php',
                    'subscribed' => false
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema against Json Response'
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
