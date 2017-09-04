<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Source;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class DeleteAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->populate(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources',
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $this->httpMethod = 'DELETE';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources';
    }

    public function testSuccess() {
        //DELETE ALL
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
                ]
            )
        );

        $response = $this->process($request);
        $body     = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('deleted', $body);
        $this->assertEquals(8, $body['deleted']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'source/deleteAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
