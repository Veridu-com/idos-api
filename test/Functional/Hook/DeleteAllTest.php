<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Hook;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresCompanyToken;

class DeleteAllTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCompanyToken;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks';
        // $this->populate($this->uri);
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/management/credentials/4c9184f37cff01bcdc32dc486ec36961/hooks',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest($environment);

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'hook/deleteAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testErrorCredentialDoesntBelongToCompany() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/management/credentials/1e772b1e4d57560422e07565600aca48/hooks',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest($environment);

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
