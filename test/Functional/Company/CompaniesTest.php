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

class CompaniesTest extends \PHPUnit_Framework_TestCase {

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


    public function testListCompanies() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies',
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

        $body = $response->getParsedBody();

        $this->assertNotEmpty($body);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $body);
        $this->assertTrue($body['status']);
        $this->assertArrayHasKey('data', $body);
        $this->assertNotEmpty($body['data']);
        $this->assertArrayHasKey('updated', $body);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'listAllCompanies',
                json_decode($response->getBody())
            )
        );
    }

    public function testListCompaniesMissingAuthorization() {
        $environment = Environment::mock(
            [
                'SCRIPT_NAME'    => '/index.php',
                'REQUEST_URI'    => '/1.0/companies',
                'REQUEST_METHOD' => 'GET',
                'QUERY_STRING' => ''
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

    public static function tearDownAfterClass() {
        $phinxApp = new \Phinx\Console\PhinxApplication();
        $phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'development');
        $phinxTextWrapper->getRollback();
    }
}
