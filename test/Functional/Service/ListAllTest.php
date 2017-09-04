<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Service;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class ListAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/companies/veridu-ltd/services';
    }

    public function testSuccess() {
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

        $this->assertSame(1321189817, $body['data'][0]['id']);
        $this->assertNotEmpty($body['data'][0]['listens']);
        $this->assertEquals(
            [
                'idos:source.amazon.created',
                'idos:source.dropbox.created',
                'idos:source.facebook.created',
                'idos:source.google.created',
                'idos:source.linkedin.created',
                'idos:source.paypal.created',
                'idos:source.spotify.created',
                'idos:source.twitter.created',
                'idos:source.yahoo.created'
            ], $body['data'][0]['listens']
        );

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'service/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
