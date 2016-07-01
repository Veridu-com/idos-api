<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Credential;

use Test\Functional\Credential\AbstractCredentialClass;
use Test\Functional\AbstractFunctionalClass;

use App\Boot\Middleware;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

class CredentialTest extends AbstractFunctionalClass {
    protected $publicKey;
    protected $response;

    public function testCreateNew() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd/credentials',
                'REQUEST_METHOD' => 'POST',
                'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
            ]
        );

        $body = new RequestBody();

        $body->write(
        	json_encode(
        		[
    				'name' => 'Very Secure',
    				'production' => false
				]
			)
		);

        $request = new Request(
            'POST',
            Uri::createFromEnvironment($environment),
            Headers::createFromEnvironment($environment),
            [],
            $environment->all(),
            $body
        );

        $response = new Response();

        $app = $this->getApp();

        $this->response = $app($request->withHeader('Content-Type', 'application/json'), $response);

        $body = json_decode($this->response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame('Very Secure', $body['data']['name']);
        $this->assertSame('very-secure', $body['data']['slug']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'credential',
                'createNew',
                json_decode($response->getBody())
            )
        );
    }

    public function testGetOne() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961',
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
        $this->assertSame('My Test Key', $body['data']['name']);
        $this->assertSame('my-test-key', $body['data']['slug']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'credential',
                'getOne',
                json_decode($response->getBody())
            )
        );
    }

    public function testUpdateOne() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961',
                'REQUEST_METHOD' => 'PUT',
                'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
            ]
        );

        $body = new RequestBody();

        $body->write(json_encode(['name' => 'Test Key']));

        $request = new Request(
            'PUT',
            Uri::createFromEnvironment($environment),
            Headers::createFromEnvironment($environment),
            [],
            $environment->all(),
            $body
        );

        $response = new Response();

        $app = $this->getApp();

        $this->response = $app($request->withHeader('Content-Type', 'application/json'), $response);

        $body = json_decode($this->response->getBody(), true);

        $this->assertNotEmpty($body);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);
        $this->assertSame('Test Key', $body['data']['name']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'credential',
                'updateOne',
                json_decode($response->getBody())
            )
        );
    }

    public function testDeleteOne() {
    	$environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd/credentials/4c9184f37cff01bcdc32dc486ec36961',
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
         */
        $this->assertTrue(
            $this->validateSchema(
                'credential',
                'deleteOne',
                json_decode($response->getBody())
            )
        );
    }

}
