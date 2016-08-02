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
        // $environment = $this->createEnvironment(
        //     [
        //         'HTTP_CONTENT_TYPE' => 'application/json'
        //     ]
        // );

        // $request = $this->createRequest(
        //     $environment,
        //     json_encode(
        //         [
        //             'userName' => 'New Member',
        //             'role' => 'admin',
        //         ]
        //     )
        // );

        // $response = $this->process($request);

        // $body = json_decode($response->getBody(), true);

        // var_dump($body);
        // exit;

        // $this->assertNotEmpty($body);

        // $this->assertEquals(201, $response->getStatusCode());

        // $this->assertTrue($body['status']);
        // $this->assertSame('admin', $body['data']['role']);
        // $this->assertSame('New Member', $body['data']['user']['userName']);
        // /*
        //  * Validates Json Schema against Json Response'
        //  */
        // $this->assertTrue(
        //     $this->validateSchema(
        //         'member/createNew.json',
        //         json_decode($response->getBody())
        //     ),
        //         $this->schemaErrors
        //     );

    }
}
