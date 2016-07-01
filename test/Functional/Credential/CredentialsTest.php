<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Credential;

use Test\Functional\AbstractFunctionalClass;

use App\Boot\Middleware;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

class CredentialsTest extends AbstractFunctionalClass {

    public function testListAll() {
    	$environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd/credentials',
                'REQUEST_METHOD' => 'GET',
                'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
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
                'credential',
                'listAll',
                json_decode($response->getBody())
            )
        );

    }

    public function testDeleteAll() {
		$environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd/credentials',
                'REQUEST_METHOD' => 'DELETE',
                'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
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
        $this->assertTrue($body['status']);
        $this->assertEquals(1, $body['deleted']);

        /*
         * Validates Json Schema with Json Response
         * Params: entity schema, method, data
         */
        $this->assertTrue(
            $this->validateSchema(
                'credentials',
                'deleteAll',
                json_decode($response->getBody())
            )
        );
    }

}
