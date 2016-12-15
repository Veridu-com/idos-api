<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Sso;

use Interop\Container\ContainerInterface;
use OAuth\Common\Storage\Memory;
use OAuth\OAuth2\Service\Facebook;
use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;

class GetOneTest extends AbstractFunctional {
    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/sso/facebook';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
            ]
        );

        $request = $this->createRequest($environment);
        $response = $this->process($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('enabled', $body['data']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('sso/getOne.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}
