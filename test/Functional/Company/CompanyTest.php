<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Company;

use App\Boot\Middleware;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

//Schema validator
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;

class CompanyTest extends \PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        $phinxApp = new \Phinx\Console\PhinxApplication();
        $phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'development');
        $phinxTextWrapper->getMigrate();
        $phinxTextWrapper->getSeed();
    }

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

    protected function validateSchema($schemaName, $bodyResponse) {
        $resolver = new RefResolver(new UriRetriever(), new UriResolver());
        $schema = $resolver->resolve(
            sprintf(
                'file://' . __DIR__ .'/../Schemas/Company/%s.json',
                $schemaName
            )
        );
        $validator = new Validator();

        $validator->check(
            $bodyResponse,
            $schema
        );

        return $validator->isValid();
    }


    public function testCreateCompany() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies',
                'REQUEST_METHOD' => 'POST',
                'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
            ]
        );

        $body = new RequestBody();

        $body->write(json_encode(['name' => 'Melan Ltd.']));

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
        $this->assertArrayHasKey('status', $body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('name', $body['data']);
        $this->assertArrayHasKey('slug', $body['data']);
        $this->assertArrayHasKey('public_key', $body['data']);
        $this->assertArrayHasKey('created_at', $body['data']);
        $this->assertSame('Melan Ltd.', $body['data']['name']);
        $this->assertSame('melan-ltd', $body['data']['slug']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'createCompany',
                json_decode($response->getBody())
            )
        );
    }


    public function testDeleteCompany() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/melan-ltd',
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
        $this->assertArrayHasKey('status', $body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('deleted', $body);
        $this->assertEquals(1, $body['deleted']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'deleteCompany',
                json_decode($response->getBody())
            )
        );
    }

    public function testGetCompany() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd',
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
        $this->assertArrayHasKey('status', $body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('name', $body['data']);
        $this->assertArrayHasKey('slug', $body['data']);
        $this->assertArrayHasKey('public_key', $body['data']);
        $this->assertArrayHasKey('created_at', $body['data']);
        $this->assertArrayHasKey('updated', $body);
        $this->assertSame('Veridu Ltd', $body['data']['name']);
        $this->assertSame('veridu-ltd', $body['data']['slug']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'getCompany',
                json_decode($response->getBody())
            )
        );

    }

    public function testUpdateCompany() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/veridu-ltd',
                'REQUEST_METHOD' => 'PUT',
                'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
            ]
        );

        $body = new RequestBody();

        $body->write(json_encode(['name' => 'Veridu Ltd.']));

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
        $this->assertArrayHasKey('status', $body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('updated', $body);
        $this->assertArrayHasKey('name', $body['data']);
        $this->assertArrayHasKey('slug', $body['data']);
        $this->assertArrayHasKey('public_key', $body['data']);
        $this->assertArrayHasKey('created_at', $body['data']);
        $this->assertSame('Veridu Ltd.', $body['data']['name']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'updateCompany',
                json_decode($response->getBody())
            )
        );
    }

    public static function tearDownAfterClass() {
        $phinxApp = new \Phinx\Console\PhinxApplication();
        $phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'development');
        $phinxTextWrapper->getRollback();
    }
}
