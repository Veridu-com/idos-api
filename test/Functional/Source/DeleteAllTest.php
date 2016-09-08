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
use Test\Functional\Traits;

class DeleteAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresUserToken,
        Traits\RequiresCredentialToken,
        Traits\RejectsCompanyToken;

    protected function setUp() {
        parent::setUp();
    
        $this->httpMethod = 'DELETE';

        $this->populate(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources',
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );
        $this->entity = $this->getRandomEntity();
        $this->uri    = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources';
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->userTokenHeader()
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
                'source/deleteAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
