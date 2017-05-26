<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth\Identity;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;

class SuccessHeaderTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        $this->uri           = '/testSuccessHeader';
        $this->httpMethod    = 'GET';
    }

    public function testSuccessHeader() {
        $token = Token::generateIdentityToken(
            '5d41402abc4b2a76b9719d911017c592',
            '7d793037a0760186574b0282f2f435e7'
        );

        $authMiddleware = $this->middlewareApp
            ->getContainer()
            ->get('authMiddleware');

        $this->middlewareApp
            ->get(
                '/testSuccessHeader', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $identity = $request->getAttribute('identity');

                    $data = [
                        'identity' => $identity->serialize(),
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::IDENTITY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => sprintf('IdentityToken %s', $token)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertSame(
            '5d41402abc4b2a76b9719d911017c592',
            $body['identity']['public_key']
        );
        $this->assertNotEmpty($body['identity']['private_key']);
    }
}
