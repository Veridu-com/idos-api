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
        Traits\RequiresCompanyToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();
    
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/services';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'name'          => 'New service name',
                    'url'           => 'http://service-url.com',
                    'enabled'       => true,
                    'access'        => 1,
                    'auth_username' => 'idos',
                    'auth_password' => 'secret',
                    'listens'       => [
                        'source.add.facebook'
                    ],
                    'triggers' => [
                        'source.scraper.facebook.finished'
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
