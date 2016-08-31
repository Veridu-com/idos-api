<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Gate;

use App\Helper\Token;
use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthMiddleware;
use Test\Functional\Traits\HasAuthCredentialToken;

class UpdateOneTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'PUT';
        $this->userName = 'f67b96dcf96b49d713a520ce9f54053c';
        $this->populate(
            sprintf('/1.0/profiles/%s/gates', $this->userName),
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );
        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/profiles/%s/gates/%s', $this->userName, $this->entity['slug']);
    }

    /**
     * @group joe
     */
    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $newPass = true;
        $request  = $this->createRequest($environment, json_encode(['pass' => $newPass]));

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame($newPass, $body['data']['pass']);

        /*
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/updateOne.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = sprintf('/1.0/profiles/%s/gates/dummy-ltd', $this->userName);

        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request = $this->createRequest($environment, json_encode(['pass' => false]));

        $response = $this->process($request);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse($body['status']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
