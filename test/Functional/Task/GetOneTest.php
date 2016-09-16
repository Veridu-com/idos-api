<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Task;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RejectsIdentityToken;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresCredentialToken;

class GetOneTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCredentialToken;
    use RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->populate(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/processes',
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );
        $this->process = $this->getRandomEntity();

        $this->populate(
            sprintf('/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/processes/%s/tasks', $this->process['id']),
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );
        $this->task = $this->getRandomEntity();

        $this->uri = sprintf(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/processes/%s/tasks/%s',
            $this->process['id'],
            $this->task['id']
        );
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
                ]
            )
        );
        $response = $this->process($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertEquals($this->task, $body['data']); // asserts it fetches the right entity

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'task/getOne.json',
                json_decode(
                    (string) $response->getBody()
                )
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/processes/%s/tasks/1212',
            $this->process['id']
        );

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
