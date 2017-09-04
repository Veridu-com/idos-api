<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Task;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RejectsIdentityToken;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresCredentialToken;

class CreateNewTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCredentialToken;
    use RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/processes/1321189817/tasks';
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
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('task/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testEmptyName() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = '';
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

        $this->assertSame(400, $response->getStatusCode());

        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testEmptyEvent() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'Test';
        $event   = '';
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

        $this->assertSame(400, $response->getStatusCode());

        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testInvalidRunning() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'Test';
        $event   = 'test';
        $running = 'not a boolean';

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

        $this->assertSame(400, $response->getStatusCode());

        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testInvalidSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $name    = 'Test';
        $event   = 'test';
        $running = true;
        $success = 'not a boolean';

        $request = $this->createRequest(
            $environment, json_encode(
                [
                    'name'    => $name,
                    'event'   => $event,
                    'running' => $running,
                    'success' => $success
                ]
            )
        );
        $response = $this->process($request);

        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(400, $response->getStatusCode());

        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}
