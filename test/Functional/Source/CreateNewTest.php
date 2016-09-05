<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Source;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresUserToken;

class CreateNewTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresUserToken;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            "name" => "email", 
            "tags" => [
                "otp_check" => "email"
            ]
        ];

        $request = $this->createRequest(
            $environment, 
            json_encode($data)
        );

        $response = $this->process($request);

        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($data['name'], $body['data']['name']);
        $this->assertSame([], $body['data']['tags']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema('source/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}
