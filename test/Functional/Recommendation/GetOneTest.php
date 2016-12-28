<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Recommendation;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class GetOneTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresUserToken,
        Traits\RejectsCredentialToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->uri        = sprintf('/1.0/profiles/%s/recommendation', $this->userName);
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

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'recommendation/getOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
