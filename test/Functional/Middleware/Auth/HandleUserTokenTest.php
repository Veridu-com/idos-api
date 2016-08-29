<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Middleware\Auth;

use Slim\App;
use App\Middleware\Auth;
use App\Repository\DBUser;
use Test\Functional\AbstractFunctional;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class HandleUserTokenTest extends AbstractFunctional {

    /**
     * Slim's Application Instance.
     *
     * @var \Slim\App
     */
    private $app;

    /**
     * Load all the dependencies for the aplication.
     *
     * @return Slim\App $app
     */
    protected function getApp() : App {
        if ($this->app) {
            return $this->app;
        }

        $app = new App(
            ['settings' => $GLOBALS['appSettings']]
        );

        require_once __ROOT__ . '/../config/dependencies.php';
        require_once __ROOT__ . '/../config/handlers.php';

        $this->app = $app;

        return $app;
    }

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/';
        
    }

    public function testSuccess() {
        $token = DBUser::generateToken('JohnDoe', md5('private'), md5('public'));

        $container = $this->getApp()->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                $actingUser = $request->getAttribute('actingUser');
                $company = $request->getAttribute('company');
                $credential = $request->getAttribute('credential');

                $data = [
                    'actingUser' => $actingUser->serialize(),
                    'company' => $company->serialize(),
                    'credential' => $credential->serialize()
                ];

                return $response->withJson($data, 200);
            })
            ->add($authMiddleware(Auth::USER_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'userToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertSame('JohnDoe', $body['actingUser']['username']);
        $this->assertSame($body['credential']['id'], $body['actingUser']['credential_id']);

        $this->assertSame(md5('public'), $body['credential']['public']);
        $this->assertSame('secure:' . md5('private'), $body['credential']['private']);
        $this->assertSame($body['company']['id'], $body['credential']['company_id']);
    }

    public function testInvalidToken() {
        $token = 'invalid.token';

        $container = $this->getApp()->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::USER_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'userToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Token', $body['error']['message']);
    }

    public function testInvalidCredential() {
        $token = DBUser::generateToken('JohnDoe', md5('private'), md5('invalid-public'));

        $container = $this->getApp()->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::USER_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'userToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Credential', $body['error']['message']);
    }

    public function testInvalidTokenSign() {
        $token = DBUser::generateToken('JohnDoe', md5('invalid-private'), md5('public'));

        $container = $this->getApp()->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::USER_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'userToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Token Verification Failed', $body['error']['message']);
    }

    public function testNullSubject() {
        $token = DBUser::generateToken(null, md5('private'), md5('public'));

        $container = $this->getApp()->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::USER_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'userToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }

    public function testEmptySubject() {
        $token = DBUser::generateToken('', md5('private'), md5('public'));

        $container = $this->getApp()->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::USER_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'userToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }

    public function testEmptySubject() {
        $token = DBUser::generateToken('', md5('private'), md5('public'));

        $container = $this->getApp()->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $this->getApp()
            ->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            })
            ->add($authMiddleware(Auth::USER_TOKEN));

        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'userToken=' . $token
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }
}
