<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Review;

use Slim\Http\Response;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RequiresCompanyToken;
use Test\Functional\Traits\RequiresAuth;

class CreateNewTest extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCompanyToken;

    protected function setUp() {
        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/reviews';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->companyTokenHeader()
            ]
        );

        $positive = true;
        $request  = $this->createRequest(
            $environment, json_encode(
                [
                    'warning_id' => 1321189817,
                    'positive'  => $positive
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
         * Validates Json Schema against Json Response'
         */
        $this->assertTrue(
            $this->validateSchema('review/createNew.json', json_decode($response->getBody())),
            $this->schemaErrors
        );
    }
}
