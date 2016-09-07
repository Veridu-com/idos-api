<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Test\Functional\Traits;

class ListAllTest extends AbstractRawFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsCompanyToken;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/1321189817/raw';
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

        $this->assertCount(3, $body['data']);

        foreach ($body['data'] as $raw) {
            $this->assertContains($raw['collection'], ['raw-1', 'raw-2', 'raw-3']);
            $this->assertContains($raw['data'], ['data-1', 'data-2', 'data-3']);
        }

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilter() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'collections=raw-1'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        $this->assertCount(1, $body['data']);

        foreach ($body['data'] as $raw) {
            $this->assertContains($raw['collection'], ['raw-1']);
            $this->assertContains($raw['data'], ['data-1']);
        }

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilterMultiple() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'collections=raw-1,raw-3'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(2, $body['data']);

        foreach ($body['data'] as $raw) {
            $this->assertContains($raw['collection'], ['raw-1', 'raw-3']);
            $this->assertContains($raw['data'], ['data-1', 'data-3']);
        }

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
