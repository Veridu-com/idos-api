<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Member;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies/veridu-ltd/members';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json'
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'credential' => '4c9184f37cff01bcdc32dc486ec36961',
                    'userName' => '9fd9f63e0d6487537569075da85a0c7f2',
                    'role'     => 'admin',
                ]
            )
        );

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertTrue($body['status']);
        $this->assertSame('admin', $body['data']['role']);
        $this->assertSame('9fd9f63e0d6487537569075da85a0c7f2', $body['data']['user']['username']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'member/createNew.json',
                json_decode($response->getBody())
            ),
                $this->schemaErrors
            );

    }
}
