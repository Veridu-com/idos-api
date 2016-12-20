<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Service;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class CreateNewTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();
    }

    public function testSuccess() {
        $handler_service_id = 1321189817;

        $this->httpMethod = 'DELETE';
        $this->uri = sprintf('/1.0/companies/veridu-ltd/services/%s', $handler_service_id);

        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest($environment);

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies/veridu-ltd/services';

        $environment = $this->createEnvironment(
            [   
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'handler_service_id' => $handler_service_id,
                    'listens' => [
                        'idos:source.amazon.created',
                        'idos:source.dropbox.created',
                        'idos:source.facebook.created',
                        'idos:source.google.created',
                        'idos:source.linkedin.created',
                        'idos:source.paypal.created',
                        'idos:source.spotify.created',
                        'idos:source.twitter.created',
                        'idos:source.yahoo.created'
                    ]
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertNotEmpty($body['data']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'service/createNew.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
