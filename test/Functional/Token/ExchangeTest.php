<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Token;

use App\Helper\Token as TokenHelper;
use App\Middleware\Auth;
use Slim\Http\Uri;
use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class ExchangeTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresUserToken,
        Traits\RejectsCompanyToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/token';
    }

    public function testSuccess() {
        /**
         * First part, where we make a request to exchange out user token by a company token.
         */
        $token       = TokenHelper::generateUserToken(md5('JohnDoe1'), md5('public'), md5('private'));
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader($token)
            ]
        );

        $request = $this->createRequest(
            $environment,
            json_encode(
                [
                    'slug' => 'veridu-ltd'
                ]
            )
        );

        $response = $this->process($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertNotEmpty($body['data']);

        $companyToken = $body['data'];

        /**
         * Second part, decode the token and test for its integrity.
         */
        $authMiddleware = $this->getApp()->getContainer()->get('authMiddleware')(Auth::IDENTITY);
        $reflection     = new \ReflectionClass($authMiddleware);
        $method         = $reflection->getMethod('handleCompanyToken');
        $method->setAccessible(true);

        $request = $method->invokeArgs($authMiddleware, [$request, $companyToken]);

        $company    = $request->getAttribute('company');
        $actingUser = $request->getAttribute('user');
        $credential = $request->getAttribute('credential');

        $this->assertSame($company->id, $credential->companyId);
        $this->assertSame($credential->id, $actingUser->credentialId);
        $this->assertTrue(strpos($actingUser->role, 'company') === 0);

    }
}
