<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Test\Functional\Traits;

class DeleteOneTest extends AbstractRawFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsCompanyToken;

    protected function setUp() {
        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/1321189817/raw/raw-2';

        $this->populateDb();
    }

    public function testSuccess() {
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

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/deleteOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/1321189817/raw/00000';
        $request   = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
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
                'raw/deleteOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
