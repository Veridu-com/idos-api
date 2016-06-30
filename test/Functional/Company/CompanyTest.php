<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Company;

use App\Boot\Middleware;
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Slim\App;
//Schema validator
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
//Phinx Migration and Seed
use Slim\Http\Response;
use Slim\Http\Uri;

class CompanyTest extends \PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
        $phinxApp         = new PhinxApplication();
        $phinxTextWrapper = new TextWrapper($phinxApp);
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'development');
        $phinxTextWrapper->getRollback('development', 0);
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
        $schema   = $resolver->resolve(
            sprintf(
                'file://' . __DIR__ . '/../../../schema/company/%s.json',
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

        $body->write(json_encode(['name' => 'NewCompany Ltd.']));

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
        $this->assertSame('NewCompany Ltd.', $body['data']['name']);
        $this->assertSame('newcompany-ltd', $body['data']['slug']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'createNew',
                json_decode($response->getBody())
            )
        );
    }

    public function testDeleteOneCompany() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies/app-deck',
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
                'deleteOne',
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
        $this->assertTrue($body['status']);
        $this->assertSame('Veridu Ltd', $body['data']['name']);
        $this->assertSame('veridu-ltd', $body['data']['slug']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'getOne',
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
        $this->assertTrue($body['status']);
        $this->assertSame('Veridu Ltd.', $body['data']['name']);

        /*
         * Validates Json Schema with Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'updateOne',
                json_decode($response->getBody())
            )
        );
    }

    public static function tearDownAfterClass() {
        $phinxApp         = new PhinxApplication();
        $phinxTextWrapper = new TextWrapper($phinxApp);
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'development');
        $phinxTextWrapper->getRollback();
    }
}
