<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Company;

use Test\Functional\AbstractFunctional;
use App\Boot\Middleware;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

class MainTest extends AbstractFunctional {
    public function testListEndpoints() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/',
                'REQUEST_METHOD' => 'GET',
                'QUERY_STRING'   => ''
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
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
