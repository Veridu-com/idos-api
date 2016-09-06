<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Traits;

trait RejectsCompanyToken {
    public function testCompanyTokenHeaderRejection() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => $this->uri,
                'REQUEST_METHOD'     => $this->httpMethod,
                'HTTP_AUTHORIZATION' => 'CompanyToken dummy'
            ]
        );
        $request  = $this->createRequest($environment);
        $response = $this->process($request);
        $this->assertSame(401, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testCompanyTokenQueryStringRejection() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'    => $this->uri,
                'REQUEST_METHOD' => $this->httpMethod,
                'QUERY_STRING'   => 'companyToken=dummy'
            ]
        );
        $request  = $this->createRequest($environment);
        $response = $this->process($request);
        $this->assertSame(401, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema against Json Response
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
