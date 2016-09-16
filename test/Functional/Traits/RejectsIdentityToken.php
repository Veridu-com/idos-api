<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Traits;

trait RejectsIdentityToken {
    public function testIdentityTokenHeaderRejection() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => $this->uri,
                'REQUEST_METHOD'     => $this->httpMethod,
                'HTTP_AUTHORIZATION' => 'IdentityToken dummy'
            ]
        );
        $request  = $this->createRequest($environment);
        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

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

    public function testIdentityTokenQueryStringRejection() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'    => $this->uri,
                'REQUEST_METHOD' => $this->httpMethod,
                'QUERY_STRING'   => 'identityToken=dummy'
            ]
        );
        $request  = $this->createRequest($environment);
        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

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
