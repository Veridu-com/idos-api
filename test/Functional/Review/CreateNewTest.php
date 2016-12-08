<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Review;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class CreateNewTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/companies/veridu-ltd/profiles/517015180/reviews';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $positive = true;
        $request  = $this->createRequest(
            $environment, json_encode(
                [
                    'flag_id'  => 1860914067,
                    'positive' => $positive
                ]
            )
        );
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame($positive, $body['data']['positive']);
        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('review/createNew.json', json_decode($response->getBody())),
            $this->schemaErrors
        );
    }

    public function testInvalidFlag() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
            ]
        );

        $positive = 'flag';
        $request  = $this->createRequest(
            $environment, json_encode(
                [
                    'flag_id'  => 1321189817,
                    'positive' => $positive
                ]
            )
        );
        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode($response->getBody())),
            $this->schemaErrors
        );
    }
}
