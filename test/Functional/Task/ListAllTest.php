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

class ListAllTest extends AbstractFunctional {
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

        $this->uri = sprintf(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/processes/%s/tasks',
            $this->process['id']
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
        $this->assertCount(2, $body['data']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'task/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
