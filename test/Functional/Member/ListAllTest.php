<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Member;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCompanyPrivKey;
use Test\Functional\Traits\HasAuthMiddleware;

class ListAllTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCompanyPrivKey;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/companies/veridu-ltd/members';
    }

    public function testSuccess() {
        $request = $this->createRequest($this->createEnvironment());

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'member/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
