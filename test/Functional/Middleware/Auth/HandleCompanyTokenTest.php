<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HandleCompanyTokenTest extends AbstractAuthFunctional {
    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->uri        = '/';

    }

    public function testSuccessQueryString() {
        $token = Token::generateCompanyToken(
            implode(':', [md5('public'), 'JohnDoe']),
            md5('veridu-ltd'),
            md5('dtl-udirev')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $company = $request->getAttribute('company');
                    $user = $request->getAttribute('user');
                    $credential = $request->getAttribute('credential');

                    $data = [
                        'company'    => $company->serialize(),
                        'user'       => $user->serialize(),
                        'credential' => $credential->serialize()
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertSame(
            'veridu-ltd',
            $body['company']['slug']
        );
        $this->assertSame(
            md5('veridu-ltd'),
            $body['company']['public_key']
        );
        $this->assertSame(
            'secure:' . md5('dtl-udirev'),
            $body['company']['private_key']
        );

        $this->assertSame(
            'JohnDoe',
            $body['user']['username']
        );
        $this->assertSame(
            $body['credential']['id'],
            $body['user']['credential_id']
        );

        $this->assertSame(
            md5('public'),
            $body['credential']['public']
        );
        $this->assertSame(
            $body['company']['id'],
            $body['credential']['company_id']
        );
    }

    public function testSuccessHeader() {
        $token = Token::generateCompanyToken(
            implode(':', [md5('public'), 'JohnDoe']),
            md5('veridu-ltd'),
            md5('dtl-udirev')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $company = $request->getAttribute('company');
                    $user = $request->getAttribute('user');
                    $credential = $request->getAttribute('credential');

                    $data = [
                    'company'    => $company->serialize(),
                    'user'       => $user->serialize(),
                    'credential' => $credential->serialize()
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => sprintf('CompanyToken %s', $token)
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertSame(
            'veridu-ltd',
            $body['company']['slug']
        );
        $this->assertSame(
            md5('veridu-ltd'),
            $body['company']['public_key']
        );
        $this->assertSame(
            'secure:' . md5('dtl-udirev'),
            $body['company']['private_key']
        );

        $this->assertSame(
            'JohnDoe',
            $body['user']['username']
        );
        $this->assertSame(
            $body['credential']['id'],
            $body['user']['credential_id']
        );

        $this->assertSame(
            md5('public'),
            $body['credential']['public']
        );
        $this->assertSame(
            $body['company']['id'],
            $body['credential']['company_id']
        );
    }

    public function testSuccessNoSubject() {
        $token = Token::generateCompanyToken(
            null,
            md5('veridu-ltd'),
            md5('dtl-udirev')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    $company = $request->getAttribute('company');
                    $user = $request->getAttribute('user');
                    $credential = $request->getAttribute('credential');

                    $data = [
                        'company'    => $company->serialize(),
                        'user'       => $user !== null ? true : false,
                        'credential' => $credential !== null ? true : false
                    ];

                    return $response->withJson($data, 200);
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertSame(
            'veridu-ltd',
            $body['company']['slug']
        );
        $this->assertSame(
            md5('veridu-ltd'),
            $body['company']['public_key']
        );
        $this->assertSame(
            'secure:' . md5('dtl-udirev'),
            $body['company']['private_key']
        );

        $this->assertFalse($body['user']);
        $this->assertFalse($body['credential']);
    }

    public function testInvalidToken() {
        $token = 'invalid.token';

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Token',
            $body['error']['message']
        );
    }

    public function testInvalidCredential() {
        $token = Token::generateCompanyToken(
            implode(':', [md5('public'), 'JohnDoe']),
            md5('invalid-public'),
            md5('dtl-udirev')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Company',
            $body['error']['message']
        );
    }

    public function testInvalidTokenSign() {
        $token = Token::generateCompanyToken(
            implode(':', [md5('public'), 'JohnDoe']),
            md5('veridu-ltd'),
            md5('invalid-private')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Token Verification Failed',
            $body['error']['message']
        );
    }

    public function testInvalidSubjectFormat() {
        $token = Token::generateCompanyToken(
            implode('.', [md5('public'), 'JohnDoe']),
            md5('veridu-ltd'),
            md5('dtl-udirev')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Subject',
            $body['error']['message']
        );
    }

    public function testInvalidSubjectCredential() {
        $token = Token::generateCompanyToken(
            implode(':', [md5('invalid-public'), 'JohnDoe']),
            md5('veridu-ltd'),
            md5('dtl-udirev')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Credential Public Key',
            $body['error']['message']
        );
    }

    public function testInvalidSubjectUsername() {
        $token = Token::generateCompanyToken(
            implode(':', [md5('public'), 'John*Doe']),
            md5('veridu-ltd'),
            md5('dtl-udirev')
        );

        $authMiddleware = $this->getApp()
            ->getContainer()
            ->get('authMiddleware');
        $this->getApp()
            ->get(
                '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::COMPANY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'companyToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Subject Username',
            $body['error']['message']
        );
    }
}
