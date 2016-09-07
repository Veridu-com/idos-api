<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Member;

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
        $this->uri        = '/1.0/management/members';
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
                    'credential' => '4c9184f37cff01bcdc32dc486ec36961',
                    'userName'   => 'f67b96dcf96b49d713a520ce9f54053c',
                    'role'       => 'admin',
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame('admin', $body['data']['role']);
        $this->assertSame('f67b96dcf96b49d713a520ce9f54053c', $body['data']['user']['username']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'member/createNew.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
