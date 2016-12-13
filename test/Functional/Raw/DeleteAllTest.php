<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class DeleteAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();
    }

    public function testSuccess() {
        //lists all raw data to count number of data to be deleted
        $this->httpMethod = 'GET';
        $this->uri        = sprintf('/1.0/profiles/%s/raw', $this->userName);

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);        
        
        $totalMembers = count($body['data']);

        $this->httpMethod = 'DELETE';
        $this->uri        = sprintf('/1.0/profiles/%s/raw', $this->userName);
        
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('deleted', $body['data']);
        $this->assertEquals($totalMembers, $body['data']['deleted']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'member/deleteAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/profiles/00000/raw');

        $request   = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
                ]
            )
        );
        
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
