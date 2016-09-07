<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Company;

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
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest($environment, json_encode(['name' => 'New Company']));

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('New Company', $body['data']['name']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'company/createNew.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testCreateSpecialChars() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $request = $this->createRequest($environment, json_encode(['name' => 'Special Chårs']));

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('Special Chårs', $body['data']['name']);
        $this->assertSame('special-chars', $body['data']['slug']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'company/createNew.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
