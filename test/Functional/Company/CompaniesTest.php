<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Validator;

use App\Boot\Middleware;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

class CompaniesTest extends \PHPUnit_Framework_TestCase {
    protected function getApp() {
        $app = new App(
            ['settings' => $GLOBALS['appSettings']]
        );

        require_once __ROOT__ . '/../config/dependencies.php';

        require_once __ROOT__ . '/../config/middleware.php';

        require_once __ROOT__ . '/../config/handlers.php';

        require_once __ROOT__ . '/../config/routes.php';

        return $app;
    }

    public function testListCompanies() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies',
                'REQUEST_METHOD' => 'GET',
                'QUERY_STRING'   => 'companyPrivKey=testCompanyPrivKey'
            ]
        );

        $request = new Request(
            'GET',
            Uri::createFromEnvironment($environment),
            Headers::createFromEnvironment($environment),
            [],
            $environment->all(),
            new RequestBody()
        );
        $response = new Response();

        $app = $this->getApp();
        $app->process($request, $response);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('data', $body);
        $this->assertNotEmpty($body['data']);
        $this->assertArrayHasKey('updated', $body);
    }

    public function testListCompaniesMissingAuthorization() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies',
                'REQUEST_METHOD' => 'GET',
            ]
        );

        $request = new Request(
            'GET',
            Uri::createFromEnvironment($environment),
            Headers::createFromEnvironment($environment),
            [],
            $environment->all(),
            new RequestBody()
        );
        $response = new Response();

        $app = $this->getApp();
        $app->process($request, $response);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertArrayHasKey('status', $body);
        $this->assertFalse($body['status']);
        $this->assertArrayHasKey('error', $body);
        $this->assertArrayHasKey('code', $body['error']);
        $this->assertArrayHasKey('message', $body['error']);
    }

    public function testDeleteCompanies() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies',
                'REQUEST_METHOD' => 'DELETE',
            ]
        );

        $request = new Request(
            'DELETE',
            Uri::createFromEnvironment($environment),
            Headers::createFromEnvironment($environment),
            [],
            $environment->all(),
            new RequestBody()
        );
        $response = new Response();

        $app = $this->getApp();
        $app->process($request, $response);

        $body = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $body);
        $this->assertTrue($body['status']);
    }
}
