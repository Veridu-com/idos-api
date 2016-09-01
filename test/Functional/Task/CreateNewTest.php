<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Task;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCredentialToken;
use Test\Functional\Traits\HasAuthMiddleware;

class CreateNewTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/processes/1321189817';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'Testing';
        $event   = 'testing';
        $running = true;

        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'name'    => $name,
                    'event'   => $event,
                    'running' => $running
                ]
            )
        );
        $response = $this->process($request);

        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(201, $response->getStatusCode());

        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertSame($name, $body['data']['name']);
        $this->assertSame($event, $body['data']['event']);
        $this->assertSame($running, $body['data']['running']);
        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema('task/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}
