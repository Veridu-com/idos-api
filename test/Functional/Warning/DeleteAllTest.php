<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Warning;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresCredentialToken;

class DeleteAllTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'DELETE';

        $this->populate(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/warnings',
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );
        $this->entity = $this->getRandomEntity();
        $this->uri    = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/warnings';
    }

    /**
     * @group lol
     */
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
        // refreshes the $entities prop
        $this->populate($this->uri);
        // checks if all entities were deleted
        $this->assertCount(0, $this->entities);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'feature/deleteAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
